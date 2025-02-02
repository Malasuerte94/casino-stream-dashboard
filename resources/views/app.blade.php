<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
    <div class="footer-donate font-sans items-center">
        <div class="text-sm">
            Proiect realizat de <a href="https://catalin-ene.ro/">Ene Catalin @ MalaTheMan</a>
        </div>
        <div class="font-bold">
            Vrei să sprijini acest proiect? <a class="donate-button" target="_blank" href="https://revolut.me/malatheman"><span>Revolut</span></a>
        </div>
    </div>
</html>
