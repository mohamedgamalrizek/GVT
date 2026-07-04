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

        @php($siteSettings = rescue(fn () => \App\Models\WebsiteSetting::site(), \App\Models\WebsiteSetting::defaults(), false))
        @php($favicon = $siteSettings['favicon_path'] ? '/storage/'.$siteSettings['favicon_path'] : '/favicon.ico')

        <meta name="description" content="{{ $siteSettings['default_seo_description'] }}">
        <meta name="keywords" content="{{ $siteSettings['default_seo_keywords'] }}">
        <meta property="og:site_name" content="{{ $siteSettings['brand_name'] }}">
        <meta property="og:title" content="{{ $siteSettings['default_seo_title'] }}">
        <meta property="og:description" content="{{ $siteSettings['default_seo_description'] }}">
        <link rel="icon" href="{{ $favicon }}" sizes="any">
        <link rel="apple-touch-icon" href="{{ $favicon }}">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ $siteSettings['default_seo_title'] }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
