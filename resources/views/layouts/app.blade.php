<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="application-name" content="WEB-TACTIPAD">

    <title>@yield('title', 'TACTIPAD - Media Pembelajaran Braille Interaktif')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Icon-Tactipad.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <!-- Theme Flash Prevention Script -->
    <script>
        if (localStorage.getItem('tactipad_contrast') === 'true') {
            document.documentElement.classList.add('dark-theme');
            document.addEventListener('DOMContentLoaded', function() {
                document.body.classList.add('dark-theme');
                var sun = document.getElementById('themeIconSun');
                var moon = document.getElementById('themeIconMoon');
                if (sun && moon) {
                    sun.classList.add('hidden');
                    moon.classList.remove('hidden');
                }
            });
        }
    </script>

    <!-- Fonts & Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen selection:bg-blue-600 selection:text-white" id="mainBody">

    <!-- Screen Reader Live Announcements -->
    <div id="srAnnouncement" aria-live="polite" class="sr-only"></div>

    <!-- Navigation Bar Component -->
    @include('components.navbar')

    <!-- Main Page Content -->
    @yield('content')

    @stack('scripts')
</body>
</html>
