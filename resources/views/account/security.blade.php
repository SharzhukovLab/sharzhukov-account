<x-layouts.app title="Security — Sharzhukov Account">
    <div class="page-header">
        <span class="lab-eyebrow">// Security</span>
        <h1>Security</h1>
        <p>Manage your password, two-factor authentication and privacy.</p>
    </div>

    {{-- PASSWORD --}}
    <x-lab::card style="margin-bottom: 16px;">
        <h2 style="margin-top: 0;">Password</h2>

        @if (session('status') === 'password-updated')
            <x-lab::alert variant="success" style="margin-bottom: 16px;">Password updated.</x-lab::alert>
        @endif

        @if ($errors->updatePassword->any())
            <x-lab::alert variant="danger" style="margin-bottom: 16px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->updatePassword->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </x-lab::alert>
        @endif

        <form method="POST" action="{{ route('user-password.update') }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="current_password" class="lab-label">Current password</label>
                <input id="current_password" type="password" name="current_password" autocomplete="current-password" class="lab-input">
            </div>

            <div class="field">
                <label for="password" class="lab-label">New password</label>
                <input id="password" type="password" name="password" autocomplete="new-password" class="lab-input">
            </div>

            <div class="field">
                <label for="password_confirmation" class="lab-label">Confirm new password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="lab-input">
            </div>

            <x-lab::button variant="primary" type="submit">Update password</x-lab::button>
        </form>

        <p class="field-hint" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--lab-border);">
            Forgot your current password? <a href="{{ route('logout-for-reset') }}">Sign out and reset</a>.
        </p>
    </x-lab::card>

    {{-- 2FA --}}
    <x-lab::card style="margin-bottom: 16px;">
        <h2 style="margin-top: 0;">Two-factor authentication</h2>
        <p class="field-hint" style="margin-bottom: 20px;">
            Add extra security. You'll need an authenticator app like Google Authenticator, Authy, or 1Password.
        </p>

        @php
            $has2FA = !empty($user->two_factor_secret);
            $isConfirmed = $has2FA && !empty($user->two_factor_confirmed_at);
        @endphp

        @if (!$has2FA)
            <x-lab::badge variant="danger" style="margin-bottom: 16px;">Disabled</x-lab::badge>
            <form method="POST" action="{{ route('two-factor.enable') }}">
                @csrf
                <x-lab::button variant="primary" type="submit">Enable 2FA</x-lab::button>
            </form>
        @elseif (!$isConfirmed)
            <x-lab::badge variant="warning" style="margin-bottom: 16px;">Pending confirmation</x-lab::badge>
            <p style="font-size: 14px; color: var(--lab-fg-muted);">Scan the QR code with your authenticator app, then confirm with a 6-digit code.</p>

            <div style="background: var(--lab-bg-elevated); border-radius: 12px; padding: 20px; text-align: center; margin: 16px 0;">
                {!! $user->twoFactorQrCodeSvg() !!}
            </div>

            <p style="font-size: 13px; text-align: center; color: var(--lab-fg-muted); margin-bottom: 16px;">
                Or enter manually:
                <code style="padding: 4px 8px; background: var(--lab-bg-elevated); border-radius: 6px; font-family: var(--font-mono);">{{ decrypt($user->two_factor_secret) }}</code>
            </p>

            <form method="POST" action="{{ route('two-factor.confirm') }}">
                @csrf
                <div class="field">
                    <label for="code" class="lab-label">6-digit code</label>
                    <input id="code" type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required class="lab-input" style="font-family: var(--font-mono); text-align: center; letter-spacing: 0.4em;">
                </div>
                <div style="display: flex; gap: 8px;">
                    <x-lab::button variant="primary" type="submit">Confirm</x-lab::button>
                </div>
            </form>

            <form method="POST" action="{{ route('two-factor.disable') }}" style="margin-top: 12px;">
                @csrf @method('DELETE')
                <x-lab::button variant="ghost" size="sm" type="submit">Cancel setup</x-lab::button>
            </form>
        @else
            <x-lab::badge variant="success" style="margin-bottom: 16px;">Enabled</x-lab::badge>

            @if ($user->two_factor_recovery_codes)
                <details style="margin-top: 16px;">
                    <summary style="cursor: pointer; font-weight: 500; color: var(--color-brand-600); font-size: 14px;">Show recovery codes</summary>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px; margin: 14px 0;">
                        @foreach (json_decode(decrypt($user->two_factor_recovery_codes), true) as $code)
                            <div style="padding: 8px 12px; background: var(--lab-bg-elevated); border-radius: 8px; font-family: var(--font-mono); font-size: 13px;">{{ $code }}</div>
                        @endforeach
                    </div>
                    <form method="POST" action="{{ route('two-factor.regenerate-recovery-codes') }}">
                        @csrf
                        <x-lab::button variant="secondary" size="sm" type="submit">Regenerate codes</x-lab::button>
                    </form>
                </details>
            @endif

            <form method="POST" action="{{ route('two-factor.disable') }}" style="margin-top: 20px;">
                @csrf @method('DELETE')
                <x-lab::button variant="danger" size="sm" type="submit" onclick="return confirm('Disable 2FA? This weakens your account security.');">Disable 2FA</x-lab::button>
            </form>
        @endif
    </x-lab::card>

    {{-- GDPR --}}
    <x-lab::card>
        <h2 style="margin-top: 0;">Data &amp; privacy</h2>
        <p class="field-hint" style="margin-bottom: 24px;">Under GDPR, you have the right to access and delete your personal data.</p>

        <div class="card-row" style="margin-bottom: 20px;">
            <div>
                <strong>Download my data</strong>
                <small>JSON file with all your personal information.</small>
            </div>
            <x-lab::button variant="secondary" size="sm" :href="route('account.export')">Download</x-lab::button>
        </div>

        @if ($user->deletion_requested_at)
            <div style="padding-top: 20px; border-top: 1px solid var(--lab-border);">
                <strong style="color: #dc2626; font-size: 15px;">Deletion scheduled</strong>
                <p class="field-hint" style="margin-top: 4px;">
                    Requested {{ $user->deletion_requested_at->format('M d, Y') }}. Account will be removed within 30 days.
                </p>
            </div>
        @else
            <div style="padding-top: 20px; border-top: 1px solid var(--lab-border);">
                <strong style="color: #dc2626; font-size: 15px;">Delete my account</strong>
                <p class="field-hint">Account will be permanently deleted in 30 days. This cannot be undone.</p>

                <form method="POST" action="{{ route('account.deletion') }}" onsubmit="return confirm('Are you sure?');" style="margin-top: 12px;">
                    @csrf
                    <div class="field">
                        <input type="password" name="password" placeholder="Your password" required class="lab-input">
                    </div>
                    <label class="checkbox-row">
                        <input type="checkbox" name="confirm" value="1" required>
                        <span>I understand this action</span>
                    </label>
                    <x-lab::button variant="danger" size="sm" type="submit">Schedule deletion</x-lab::button>
                </form>
            </div>
        @endif
    </x-lab::card>
</x-layouts.app>