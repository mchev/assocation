<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <meta inertia name="description" content="{{ config('app.description') }}">

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

        <meta inertia name="og:image" content="{{ asset('screenshot.png') }}">
        <meta inertia name="og:title" content="{{ config('app.name') }}">
        <meta inertia name="og:description" content="{{ config('app.description') }}">
        <meta inertia name="og:url" content="{{ config('app.url') }}">
        <meta inertia name="og:type" content="website">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=open-sans:400,500,600" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=abeezee:400" rel="stylesheet" />

        @production
            <script defer src="https://stats.pegase.io/script.js" data-website-id="f0ba459b-ef50-4556-8c2e-971dc388b643"></script>
        @endproduction
        
        @routes
        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
