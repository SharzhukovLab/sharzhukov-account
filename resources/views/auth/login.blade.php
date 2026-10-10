<x-layouts.guest title="Sign in — Sharzhukov Account">
    <div style="margin-bottom: 20px;">
        <span class="lab-eyebrow">// Sign in</span>
        <h1 style="margin: 6px 0 4px;">Welcome back</h1>
        <p style="color: var(--lab-fg-muted); font-size: 14px; margin: 0;">Sign in to your Sharzhukov's Laboratory account.</p>
    </div>

    @if ($errors->any())
        <x-lab::alert variant="danger" style="margin-bottom: 16px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-lab::alert>
    @endif

    @if (session('status'))
        <x-lab::alert variant="success" style="margin-bottom: 16px;">{{ session('status') }}</x-lab::alert>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="hidden" name="return" value="{{ request('return') }}">

        <div class="field">
            <label for="email" class="lab-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="lab-input">
        </div>

        <div class="field">
            <label for="password" class="lab-label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="lab-input">
        </div>

        <label class="checkbox-row">
            <input type="checkbox" name="remember">
            <span>Remember me</span>
        </label>

        <x-lab::button variant="primary" type="submit" style="width: 100%;">Sign in</x-lab::button>
    </form>

    <div style="margin-top: 20px; display: flex; justify-content: space-between; font-size: 13px;">
        <a href="{{ route('password.request') }}">Forgot password?</a>
        <a href="{{ route('register', ['return' => request('return')]) }}">Create account</a>
    </div>
</x-layouts.guest>