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
    {{-- Favicon --}}
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

<nav class="brutal-nav">
    <div class="brutal-nav-inner">
        <a href="{{ url('/dashboard') }}" style="display:flex;align-items:center;gap:12px;text-decoration:none;">
            <img src="{{ asset('img/branding/core-white.png') }}" alt="Core" style="height:32px;width:auto;filter:drop-shadow(2px 2px 0 var(--brutal-shadow));">
            <div style="display:flex;flex-direction:column;line-height:1;">
                <span style="font-family:var(--font-sans);font-weight:900;font-size:14px;letter-spacing:-0.02em;color:var(--brutal-border);">SHARZHUKOV ACCOUNT</span>
                <span style="font-family:var(--font-mono);font-size:9px;font-weight:700;letter-spacing:0.15em;color:var(--brutal-border);opacity:.6;">// USER CENTER</span>
            </div>
        </a>

        <div style="flex:1;display:flex;gap:4px;margin-left:16px;">
            <a href="{{ url('/dashboard') }}" class="brutal-nav-link {{ request()->is('dashboard') ? 'active' : '' }}">Overview</a>
            <a href="{{ url('/security') }}"  class="brutal-nav-link {{ request()->is('security')  ? 'active' : '' }}">Security</a>
            <a href="{{ url('/sessions') }}"  class="brutal-nav-link {{ request()->is('sessions')  ? 'active' : '' }}">Sessions</a>
            <a href="{{ url('/projects') }}"  class="brutal-nav-link {{ request()->is('projects*') ? 'active' : '' }}">Projects</a>
            <a href="https://main.sharzhukov.com" class="brutal-nav-link" target="_blank">Main ↗</a>
        </div>

        <button type="button" class="lab-theme-toggle" onclick="toggleLabTheme()" aria-label="Toggle theme" style="border:2px solid var(--brutal-border);border-radius:0;box-shadow:2px 2px 0 var(--brutal-shadow);">
            <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
            <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        </button>

        @if($me)
            <div class="user-menu" id="user-menu">
                <button type="button" class="user-menu-trigger" onclick="document.getElementById('user-menu').classList.toggle('open')" style="border:2px solid var(--brutal-border);border-radius:0;filter:drop-shadow(3px 3px 0 var(--brutal-shadow));width:36px;height:36px;padding:0;overflow:hidden;background:var(--brutal-accent);display:flex;align-items:center;justify-content:center;">
                    @if($me->avatar_path)
                        <img src="{{ $me->avatar_url }}" alt="{{ $me->name }}" style="width:100%;height:100%;object-fit:cover;display:block;">
                    @else
                        <span style="font-family:var(--font-mono);font-weight:800;font-size:16px;color:#fff;line-height:1;">{{ strtoupper(mb_substr($me->name ?? 'U', 0, 1)) }}</span>
                    @endif
                </button>
                <div class="user-menu-dropdown">
                    <div class="user-menu-header">
                        @if($me->avatar_path)
                            <img src="{{ $me->avatar_url }}" alt="{{ $me->name }}">
                        @else
                            <div style="width:40px;height:40px;background:var(--brutal-accent);color:#fff;display:flex;align-items:center;justify-content:center;font-family:var(--font-mono);font-weight:800;font-size:18px;border:2px solid var(--brutal-border);">{{ strtoupper(mb_substr($me->name ?? 'U', 0, 1)) }}</div>
                        @endif
                        <div>
                            <div class="user-menu-name">{{ $me->name }}</div>
                            <div class="user-menu-username">&#64;{{ $me->username }}</div>
                        </div>
                    </div>
                    <div class="user-menu-divider"></div>
                    <a href="https://main.sharzhukov.com/admin" class="user-menu-item" target="_blank">Admin panel</a>
                    <div class="user-menu-divider"></div>
                    <a href="{{ url('/profile') }}" class="user-menu-item">My account</a>
                    <a href="https://main.sharzhukov.com/u/{{ $me->username }}" class="user-menu-item" target="_blank">Public profile &nearr;</a>
                    <a href="{{ url('/messages') }}" class="user-menu-item">My messages</a>
                    <a href="{{ url('/projects') }}" class="user-menu-item">My projects</a>
                    <a href="{{ url('/statistics') }}" class="user-menu-item">Statistics</a>
                    <div class="user-menu-divider"></div>
                    <a href="{{ route('account.export') }}" class="user-menu-item">Export my data</a>
                    <div class="user-menu-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="user-menu-item danger">Sign out</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</nav>

<main style="max-width:1200px;margin:0 auto;padding:48px 24px 64px;width:100%;">{{ $slot }}</main>

<x-lab::site-footer context="account" />

<script>
    function toggleLabTheme() {
        var d = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', d ? 'dark' : 'light');
    }
    document.addEventListener('click', function (e) {
        const menu = document.getElementById('user-menu');
        if (menu && !menu.contains(e.target)) menu.classList.remove('open');
    });
</script>
<x-lab::cookie-consent />
</body>
</html>
