<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-base-url="{{ config('app.url') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ config('app.url') }}">
    <title>{{ config('app.name', 'Restaurant POS') }}</title>

    <!-- Preload the base URL to ensure it's available immediately -->
    <script>
        window.appUrl = '{{ config('app.url') }}';
        // Ensure the URL ends with a slash
        if (!window.appUrl.endsWith('/')) {
            window.appUrl += '/';
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>
<body class="bg-gray-50 font-sans antialiased">
    @inertia

    <script>
        // Enhanced navigation handler
        document.addEventListener('click', function(event) {
            // Find the closest anchor element
            let target = event.target.closest('a');
            if (!target) return;

            const href = target.getAttribute('href');
            
            // Don't interfere with external links, anchors, or links with specific attributes
            if (target.target === '_blank' || 
                target.hasAttribute('download') || 
                !href ||
                href.startsWith('#') ||
                href.startsWith('javascript:') ||
                target.classList.contains('no-force')) {
                return;
            }

            // Prevent default behavior
            event.preventDefault();

            // Ensure we have a valid URL
            let url = new URL(href, window.appUrl);
            
            // If the URL is external, let the browser handle it normally
            if (url.origin !== window.location.origin) {
                window.location.href = url.href;
                return;
            }

            // For same-origin URLs, use a full page reload to avoid any SPA routing issues
            window.location.href = url.href;
        }, true);

        // Add a global error handler to catch any unhandled promise rejections
        window.addEventListener('error', function(event) {
            console.error('Global error:', event.error || event.message, event);
        });

        window.addEventListener('unhandledrejection', function(event) {
            console.error('Unhandled promise rejection:', event.reason);
        });

        // Ensure all links are using the correct base URL
        document.addEventListener('DOMContentLoaded', function() {
            const baseUrl = window.appUrl;
            document.querySelectorAll('a[href^="/"]').forEach(link => {
                const href = link.getAttribute('href');
                if (href && !href.startsWith('http') && !href.startsWith('//')) {
                    link.href = baseUrl + href.replace(/^\//, '');
                }
            });
        });
    </script>
</body>
</html>
