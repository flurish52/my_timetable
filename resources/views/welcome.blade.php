<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#ffffff" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="apple-mobile-web-app-title" content="Your App Name" />
<title>myTimetable</title>
    {{-- Apple Touch Icon --}}
    <link rel="apple-touch-icon" href="/icons/pwa-192x192.png" />
    <link rel="manifest" href="/build/manifest.webmanifest" />
    {{-- Vite assets (vite-plugin-pwa auto-injects the manifest link) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div id="app"></div>
{{--@vite('resources/js/app.js')--}}
</body>
</html>
