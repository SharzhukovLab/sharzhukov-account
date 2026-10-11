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

        {{-- PASSKEYS --}}
    <x-lab::card style="margin-bottom: 16px;">
        <h2 style="margin-top: 0;">Passkeys</h2>
        <p class="field-hint" style="margin-bottom: 20px;">
            Sign in without a password using Face ID, Touch ID, Windows Hello, or a hardware security key.
        </p>

        @if (session('status') === 'passkey-registered')
            <x-lab::alert variant="success" style="margin-bottom: 16px;">Passkey added.</x-lab::alert>
        @endif
        @if (session('status') === 'passkey-deleted')
            <x-lab::alert variant="success" style="margin-bottom: 16px;">Passkey removed.</x-lab::alert>
        @endif

        @php $myPasskeys = $user->passkeys()->orderByDesc('created_at')->get(); @endphp

        @if ($myPasskeys->isEmpty())
            <p style="color: var(--lab-fg-muted); font-size: 14px; margin-bottom: 20px;">No passkeys yet.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                @foreach ($myPasskeys as $pk)
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 14px; border: 2px solid var(--brutal-border); background: var(--lab-bg-elevated);">
                        <div style="min-width: 0;">
                            <div style="font-weight: 700; font-family: var(--font-mono); font-size: 14px;">{{ $pk->name }}</div>
                            <div style="color: var(--lab-fg-muted); font-size: 12px; margin-top: 4px;">
                                @if ($pk->authenticator) {{ $pk->authenticator }} · @endif
                                Added {{ $pk->created_at?->format('M d, Y') }}
                                @if ($pk->last_used_at) · Last used {{ $pk->last_used_at->diffForHumans() }} @endif
                            </div>
                        </div>
                        <form method="POST" action="{{ url('/user/passkeys/'.$pk->id) }}" onsubmit="return confirm('Remove this passkey?');" style="flex-shrink:0;">
                            @csrf @method('DELETE')
                            <button type="submit" class="lab-btn lab-btn--danger" style="font-size: 11px; padding: 8px 12px;">Remove</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif

        <button type="button" class="lab-btn lab-btn--primary" id="passkey-add-btn">
            Add passkey →
        </button>

        <p class="field-hint" style="margin-top: 16px;">
            You may be asked to confirm your password first.
        </p>
    </x-lab::card>

    <script>
    (function () {
        var addBtn   = document.getElementById('passkey-add-btn');
                var nameInput= document.getElementById('passkey-name');
        var confirmB = document.getElementById('passkey-confirm');
                var errBox   = document.getElementById('passkey-error');
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');

        if (!window.PublicKeyCredential) {
            if (addBtn) { addBtn.disabled = true; addBtn.textContent = 'Passkeys not supported'; }
            return;
        }

        function b64uToBuf(s) {
            s = s.replace(/-/g, '+').replace(/_/g, '/');
            while (s.length % 4) s += '=';
            var bin = atob(s), len = bin.length, buf = new Uint8Array(len);
            for (var i = 0; i < len; i++) buf[i] = bin.charCodeAt(i);
            return buf.buffer;
        }
        function bufToB64u(buf) {
            var bin = '', bytes = new Uint8Array(buf);
            for (var i = 0; i < bytes.byteLength; i++) bin += String.fromCharCode(bytes[i]);
            return btoa(bin).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
        }
        function prepCreate(opts) {
            opts.challenge = b64uToBuf(opts.challenge);
            opts.user.id   = b64uToBuf(opts.user.id);
            (opts.excludeCredentials || []).forEach(function (c) { c.id = b64uToBuf(c.id); });
            return opts;
        }
        function serializeCred(cred) {
            if (typeof cred.toJSON === 'function') return cred.toJSON();
            var r = cred.response;
            return {
                id: cred.id,
                rawId: bufToB64u(cred.rawId),
                type: cred.type,
                response: {
                    clientDataJSON: bufToB64u(r.clientDataJSON),
                    attestationObject: bufToB64u(r.attestationObject),
                    transports: typeof r.getTransports === 'function' ? r.getTransports() : undefined,
                },
                clientExtensionResults: typeof cred.getClientExtensionResults === 'function' ? cred.getClientExtensionResults() : {},
            };
        }
        function showErr(msg) {
            if (!errBox) { alert(msg); return; }
            errBox.textContent = msg;
            errBox.style.display = 'block';
        }

        if (addBtn) {
            addBtn.addEventListener('click', function () {
                if (errBox) errBox.style.display = 'none';
                if (nameInput) nameInput.value = 'My device';
if (nameInput) nameInput.focus();
            });
        }
        

        if (confirmB) {
            confirmB.addEventListener('click', async function () {
                var name = (nameInput && nameInput.value.trim()) || 'My device';
                confirmB.disabled = true;
                confirmB.textContent = 'Waiting...';
                try {
                    var optRes = await fetch('/user/passkeys/options', {
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfMeta.content },
                        credentials: 'same-origin',
                    });
                    if (optRes.status === 423 || optRes.redirected || optRes.status === 401 || optRes.status === 302) {
                        // 423 = password confirmation required
                        window.location.href = '/user/confirm-password';
                        return;
                    }
                    if (!optRes.ok) throw new Error('Options fetch failed: ' + optRes.status);
                    var data = await optRes.json();
                    var optObj = data.options || data.publicKey || data;
                    var publicKey = prepCreate(optObj);

                    var cred = await navigator.credentials.create({ publicKey: publicKey });

                    var payload = { name: name, credential: serializeCred(cred) };
                    var store = await fetch('/user/passkeys', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfMeta.content,
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify(payload),
                    });
                    if (!store.ok) {
                        var text = await store.text();
                        throw new Error('Store failed: ' + store.status + ' ' + text.substring(0, 200));
                    }
                    if (window.labModalClose) window.labModalClose('passkey-modal');
                    window.location.reload();
                } catch (e) {
                    showErr(e.message || 'Failed');
                    confirmB.disabled = false;
                    confirmB.textContent = 'Create →';
                }
            });
        }
    })();
    </script>


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
    {{-- Register modal (lab-ui component) --}}
        <x-lab::modal id="passkey-modal" title="Name your passkey" :closeable="false">
            <p style="margin:0 0 16px;color:var(--lab-fg-muted);font-size:14px;line-height:1.6;">
                Give it a name you'll recognize later — e.g. "MacBook Pro", "iPhone".
            </p>
            <input type="text" id="passkey-name" class="lab-input" placeholder="MacBook Pro" maxlength="60" autocomplete="off">
            <div id="passkey-error" style="display:none; color:#dc2626; font-size:13px; margin-top:10px;"></div>

            <x-slot:actions>
                <button type="button" class="lab-btn lab-btn--secondary" data-lab-modal-close>Cancel</button>
                <button type="button" class="lab-btn lab-btn--primary" id="passkey-confirm">Create →</button>
            </x-slot:actions>
        </x-lab::modal>

</x-layouts.app>