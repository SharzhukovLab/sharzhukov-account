@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? "Sharzhukov Account" }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ff5c1f">
    <script>
        (function () {
            var t = localStorage.getItem('theme');
            var dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (t === 'dark' || (!t && dark)) document.documentElement.classList.add('dark');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;background:var(--lab-bg-sunken);">

<div style="width:100%;max-width:420px;">
    <div style="text-align:center;margin-bottom:24px;">
        <a href="https://main.sharzhukov.com" class="lab-eyebrow" style="text-decoration:none;">← Back to site</a>
    </div>
    <div class="brutal-card" style="padding:32px;box-shadow:8px 8px 0 var(--brutal-shadow);">
        {{ $slot }}
    </div>
</div>

<x-lab::cookie-consent />
</body>
</html>
