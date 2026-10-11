@props(['title' => null, 'description' => null])
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

    @if($description)
        <meta name="description" content="{{ $description }}">
    @endif
    <meta name="robots" content="noindex, follow">

    <script>
        (function () {
    var V = 'v2-2026-10-11';
    if (localStorage.getItem('theme_version') !== V) {
        localStorage.removeItem('theme');
        localStorage.setItem('theme_version', V);
    }
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
    brand-url="https://sharzhukov.com"
    :show-auth="true"
    :items="[
        ['label' => 'Overview', 'url' => '/dashboard'],
        ['label' => 'Security', 'url' => '/security'],
        ['label' => 'Sessions', 'url' => '/sessions'],
        ['label' => 'Projects', 'url' => '/projects'],
        ['label' => 'Main ↗',   'url' => 'https://sharzhukov.com', 'external' => true],
    ]"
/>

<main>{{ $slot }}</main>

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