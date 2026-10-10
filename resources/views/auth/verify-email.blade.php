<x-layouts.guest title="Verify email — Sharzhukov Account">
    <div style="margin-bottom: 20px;">
        <span class="lab-eyebrow">// Verify email</span>
        <h1 style="margin: 6px 0 4px;">Verify your email</h1>
        <p style="color: var(--lab-fg-muted); font-size: 14px; margin: 0;">We sent a verification link to your email. Click it to activate your account.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <x-lab::alert variant="success" style="margin-bottom: 16px;">A new verification link has been sent.</x-lab::alert>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <x-lab::button variant="primary" type="submit" style="width: 100%;">Resend verification email</x-lab::button>
    </form>

    <div style="margin-top: 20px; text-align: center;">
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit" style="background: none; border: none; color: var(--lab-fg-muted); cursor: pointer; font-size: 13px; font-family: inherit;">Sign out</button>
        </form>
    </div>
</x-layouts.guest>