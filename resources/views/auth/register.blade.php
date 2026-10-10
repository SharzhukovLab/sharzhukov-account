<x-layouts.guest title="Create account — Sharzhukov Account">
    <div style="margin-bottom: 20px;">
        <span class="lab-eyebrow">// Register</span>
        <h1 style="margin: 6px 0 4px;">Create account</h1>
        <p style="color: var(--lab-fg-muted); font-size: 14px; margin: 0;">Join Sharzhukov's Laboratory ecosystem.</p>
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

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input type="hidden" name="return" value="{{ request('return') }}">

        <div class="field">
            <label for="name" class="lab-label">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="lab-input">
        </div>

        <div class="field">
            <label for="email" class="lab-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="lab-input">
        </div>

        <div class="field">
            <label for="password" class="lab-label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="lab-input">
        </div>

        <div class="field">
            <label for="password_confirmation" class="lab-label">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="lab-input">
        </div>

        <x-lab::button variant="primary" type="submit" style="width: 100%;">Create account</x-lab::button>
    </form>

    <div style="margin-top: 20px; text-align: center; font-size: 13px;">
        <a href="{{ route('login', ['return' => request('return')]) }}">Already have an account? Sign in</a>
    </div>
</x-layouts.guest>