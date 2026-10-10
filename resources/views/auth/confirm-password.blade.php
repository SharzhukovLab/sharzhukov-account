<x-layouts.guest title="Confirm password — Sharzhukov Account">
    <div style="margin-bottom: 20px;">
        <span class="lab-eyebrow">// Confirm</span>
        <h1 style="margin: 6px 0 4px;">Confirm password</h1>
        <p style="color: var(--lab-fg-muted); font-size: 14px; margin: 0;">This is a protected area. Confirm your password to continue.</p>
    </div>

    @if ($errors->any())
        <x-lab::alert variant="danger" style="margin-bottom: 16px;">
            <ul style="margin: 0; padding-left: 20px;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </x-lab::alert>
    @endif

    <form method="POST" action="{{ route('password.confirm.store') }}">
        @csrf
        <div class="field">
            <label for="password" class="lab-label">Password</label>
            <input id="password" type="password" name="password" required autofocus autocomplete="current-password" class="lab-input">
        </div>
        <x-lab::button variant="primary" type="submit" style="width: 100%;">Confirm</x-lab::button>
    </form>
</x-layouts.guest>