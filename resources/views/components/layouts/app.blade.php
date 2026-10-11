@props(['title' => null])
@php $me = auth()->user(); @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? "Sharzhukov Account" }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ff5c1f">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/branding/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/branding/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/branding/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <script>
        (function () {
            var t = localStorage.getItem('theme');
            if (t === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<x-lab::site-nav
    brand-title="SHARZHUKOV ACCOUNT"
    brand-sub="// USER CENTER"
    brand-url="/dashboard"
    :show-auth="false"
    :items="[
        ['label' => 'Overview', 'url' => '/dashboard'],
        ['label' => 'Security', 'url' => '/security'],
        ['label' => 'Sessions', 'url' => '/sessions'],
        ['label' => 'Projects', 'url' => '/projects'],
        ['label' => 'Main ↗',   'url' => 'https://sharzhukov.com', 'external' => true],
    ]"
>
    @if($me)
        <a href="{{ url('/profile') }}" class="brutal-nav-avatar" title="My account">
            @if($me->avatar_path)
                <img src="{{ $me->avatar_url }}" alt="{{ $me->name }}">
            @else
                <span class="brutal-nav-avatar-fallback">{{ strtoupper(mb_substr($me->name ?? 'U', 0, 1)) }}</span>
            @endif
        </a>
    @endif

    <x-slot:drawer>
        @if($me)
            <div class="brutal-nav-drawer-user">
                @if($me->avatar_path)
                    <img src="{{ $me->avatar_url }}" alt="{{ $me->name }}">
                @else
                    <span class="brutal-nav-avatar-fallback">{{ strtoupper(mb_substr($me->name ?? 'U', 0, 1)) }}</span>
                @endif
                <div class="brutal-nav-drawer-user-info">
                    <div class="brutal-nav-drawer-user-name">{{ $me->name }}</div>
                    <div class="brutal-nav-drawer-user-username">&#64;{{ $me->username }}</div>
                </div>
            </div>
        @endif

        <a href="{{ url('/profile') }}" class="brutal-nav-drawer-link">My account</a>
        <a href="https://sharzhukov.com/u/{{ $me?->username }}" target="_blank" rel="noopener" class="brutal-nav-drawer-link">Public profile ↗</a>
        <a href="{{ url('/messages') }}" class="brutal-nav-drawer-link">My messages</a>
        <a href="{{ url('/projects') }}" class="brutal-nav-drawer-link">My projects</a>
        <a href="{{ url('/statistics') }}" class="brutal-nav-drawer-link">Statistics</a>

        @if($me?->isModerator())
            <a href="https://sharzhukov.com/admin" target="_blank" rel="noopener" class="brutal-nav-drawer-link">Admin panel ↗</a>
        @endif

        <a href="{{ route('account.export') }}" class="brutal-nav-drawer-link">Export data</a>

        <form method="POST" action="{{ route('logout') }}" style="margin:8px 0 0;">
            @csrf
            <button type="submit" class="brutal-nav-drawer-link" style="width:100%;background:transparent;cursor:pointer;color:var(--brutal-accent);">Sign out</button>
        </form>
    </x-slot:drawer>
</x-lab::site-nav>

<main style="max-width:1200px;margin:0 auto;padding:48px 24px 64px;width:100%;">{{ $slot }}</main>

<x-lab::site-footer context="account" />

<script>
    function toggleLabTheme() {
        var d = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', d ? 'dark' : 'light');
    }
</script>
<x-lab::cookie-consent />
</body>
</html>