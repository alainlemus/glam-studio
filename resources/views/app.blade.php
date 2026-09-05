<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#FAF7F2">

        @php
            $siteSettings = $page['props']['site']['settings'] ?? \App\Models\SiteSetting::current();
            $siteName = data_get($siteSettings, 'site_name') ?: config('app.name', 'Glam Studio');
            $appName = config('app.name', 'Glam Studio');
            $seo = $page['props']['seo'] ?? [];
            // Misma regla que el resolver `title` de resources/js/app.ts, para que
            // el <title> coincida exacto tanto si el SSR responde como si no.
            $seoTitle = isset($seo['title']) ? "{$seo['title']} - {$appName}" : $appName;
            $seoDescription = $seo['description'] ?? (data_get($siteSettings, 'footer_description') ?: 'Salón de belleza y spa con servicios de corte, coloración, uñas, spa y más. Encuentra tu sucursal más cercana y agenda tu cita en línea.');
            $seoImage = $seo['image'] ?? data_get($siteSettings, 'logo_url');
            $seoUrl = url()->current();
            $seoNoindex = $seo['noindex'] ?? false;
            $jsonLd = $page['props']['jsonLd'] ?? null;
        @endphp

        {{-- SEO: renderizado del lado del servidor para que sea visible sin ejecutar JS
             (bots de Google, WhatsApp, Twitter, Facebook, etc.) --}}
        <meta name="description" content="{{ $seoDescription }}">
        <link rel="canonical" href="{{ $seoUrl }}">
        @if ($seoNoindex)
            <meta name="robots" content="noindex, nofollow">
        @else
            <meta name="robots" content="index, follow">
        @endif

        <meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
        <meta property="og:title" content="{{ $seoTitle }}">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url" content="{{ $seoUrl }}">
        <meta property="og:site_name" content="{{ $siteName }}">
        <meta property="og:locale" content="es_MX">
        @if ($seoImage)
            <meta property="og:image" content="{{ $seoImage }}">
        @endif

        <meta name="twitter:card" content="{{ $seoImage ? 'summary_large_image' : 'summary' }}">
        <meta name="twitter:title" content="{{ $seoTitle }}">
        <meta name="twitter:description" content="{{ $seoDescription }}">
        @if ($seoImage)
            <meta name="twitter:image" content="{{ $seoImage }}">
        @endif

        @if ($jsonLd)
            <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endif

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

        {{-- Inline style to set the HTML background color based on our theme --}}
        <style>
            html {
                background-color: #FAF7F2;
            }

            html.dark {
                background-color: #1A1410;
            }
        </style>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        {{-- Google Fonts: Playfair Display (display), Cormorant Garamond (serif), DM Sans (sans) --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400;1,9..40,500&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap"
            rel="stylesheet"
        >

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ $seoTitle }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>