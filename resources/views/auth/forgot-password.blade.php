<x-layouts.guest title="Reset password — Sharzhukov Account">
    <div style="margin-bottom: 20px;">
        <span class="lab-eyebrow">// Reset password</span>
        <h1 style="margin: 6px 0 4px;">Forgot your password?</h1>
        <p style="color: var(--lab-fg-muted); font-size: 14px; margin: 0;">Enter your email — we'll send you a reset link.</p>
    </div>

    @if (session('status'))
        <x-lab::alert variant="success" style="margin-bottom: 16px;">{{ session('status') }}</x-lab::alert>
    @endif

    @if ($errors->any())
        <x-lab::alert variant="danger" style="margin-bottom: 16px;">
            <ul style="margin: 0; padding-left: 20px;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </x-lab::alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="field">
            <label for="email" class="lab-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="lab-input">
        </div>
        <x-lab::button variant="primary" type="submit" style="width: 100%;">Send reset link</x-lab::button>
    </form>

    <div style="margin-top: 20px; text-align: center; font-size: 13px;">
        <a href="{{ route('login') }}">&larr; Back to sign in</a>
    </div>
</x-layouts.guest>