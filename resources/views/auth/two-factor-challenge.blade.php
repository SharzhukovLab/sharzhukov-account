<x-layouts.guest title="Two-factor authentication — Sharzhukov Account">
    <div style="margin-bottom: 20px;">
        <span class="lab-eyebrow">// 2FA</span>
        <h1 style="margin: 6px 0 4px;">Two-factor authentication</h1>
        <p style="color: var(--lab-fg-muted); font-size: 14px; margin: 0;">Enter the code from your authenticator app.</p>
    </div>

    @if ($errors->any())
        <x-lab::alert variant="danger" style="margin-bottom: 16px;">
            <ul style="margin: 0; padding-left: 20px;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </x-lab::alert>
    @endif

    <form method="POST" action="{{ route('two-factor.login') }}">
        @csrf
        <div class="field">
            <label for="code" class="lab-label">6-digit code</label>
            <input id="code" type="text" name="code" inputmode="numeric" autofocus autocomplete="one-time-code" placeholder="000000" class="lab-input" style="font-family: var(--font-mono); text-align: center; letter-spacing: 0.4em; font-size: 18px;">
            <div class="field-hint">From Google Authenticator or another TOTP app.</div>
        </div>
        <x-lab::button variant="primary" type="submit" style="width: 100%;">Verify code</x-lab::button>
    </form>

    <div style="margin: 20px 0; text-align: center; color: var(--lab-fg-subtle); font-size: 12px; font-family: var(--font-mono); letter-spacing: 0.1em;">OR</div>

    <form method="POST" action="{{ route('two-factor.login') }}">
        @csrf
        <div class="field">
            <label for="recovery_code" class="lab-label">Recovery code</label>
            <input id="recovery_code" type="text" name="recovery_code" autocomplete="one-time-code" class="lab-input" style="font-family: var(--font-mono);">
            <div class="field-hint">One of your saved recovery codes.</div>
        </div>
        <x-lab::button variant="secondary" type="submit" style="width: 100%;">Sign in with recovery code</x-lab::button>
    </form>
</x-layouts.guest>