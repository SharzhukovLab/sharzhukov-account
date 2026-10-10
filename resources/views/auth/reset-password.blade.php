<x-layouts.guest title="New password — Sharzhukov Account">
    <div style="margin-bottom: 20px;">
        <span class="lab-eyebrow">// New password</span>
        <h1 style="margin: 6px 0 4px;">Set a new password</h1>
    </div>

    @if ($errors->any())
        <x-lab::alert variant="danger" style="margin-bottom: 16px;">
            <ul style="margin: 0; padding-left: 20px;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </x-lab::alert>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="field">
            <label for="email" class="lab-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="lab-input">
        </div>
        <div class="field">
            <label for="password" class="lab-label">New password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="lab-input">
        </div>
        <div class="field">
            <label for="password_confirmation" class="lab-label">Confirm new password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="lab-input">
        </div>
        <x-lab::button variant="primary" type="submit" style="width: 100%;">Reset password</x-lab::button>
    </form>
</x-layouts.guest>