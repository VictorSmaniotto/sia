<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'SIA - Sistema ITSM') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

        <!-- Ziggy Routes -->
        <script>
            window.route = function(name, params = {}) {
                const routes = {
                    'incidentes.index': '/incidentes',
                    'incidentes.create': '/incidentes/create',
                    'incidentes.show': (id) => `/incidentes/${id}`,
                    'incidentes.edit': (id) => `/incidentes/${id}/edit`,
                    'problemas.index': '/problemas',
                    'problemas.create': '/problemas/create',
                    'artigos-kb.index': '/artigos-kb',
                    'artigos-kb.create': '/artigos-kb/create'
                };

                const route = routes[name];
                if (typeof route === 'function') {
                    return route(params);
                }
                return route || '#';
            };
        </script>
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
