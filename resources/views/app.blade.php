<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Google tag starts here (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-T0MW452B3L"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-T0MW452B3L');
    </script>

    <!-- Google tag ends here (gtag.js) -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2501992701733410"
            crossorigin="anonymous"></script>

    <!--Adsense code ends here -->
     <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title inertia>{{ config('app.name', 'myTimetable') }}</title>

    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#ffffff" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="apple-mobile-web-app-title" content="myTimetable" />

    {{-- Apple Touch Icon --}}
    <link rel="apple-touch-icon" href="/icons/pwa-192x192.png" />
    <link rel="manifest" href="/build/manifest.webmanifest" />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Fraunces:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @routes
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
    @inertiaHead
</head>

<body class="antialiased">
@inertia
</body>
</html>
