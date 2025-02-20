<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
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
<body class="font-sans antialiased text-gray-100 flex flex-col min-h-screen bg-gradient-to-br from-indigo-800 via-gray-800 to-indigo-900">
<main class="flex-grow">
    @inertia
</main>
<footer class="footer-donate bg-gray-900 border-gray-700 py-3">
    <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between space-y-2 sm:space-y-0">
        <div class="text-xs text-gray-400">
            Proiect realizat de
            <a class="underline text-indigo-400" href="https://catalin-ene.ro/" target="_blank">
                Ene Catalin @ MalaTheMan
            </a>
        </div>
        <div class="font-bold text-xs">
            Vrei să sprijini acest proiect?
            <a class="donate-button inline-block bg-indigo-600 hover:bg-indigo-700 text-white py-1 px-2 rounded transition-colors duration-200" target="_blank" href="https://revolut.me/malatheman">
                Revolut
            </a>
        </div>
    </div>
</footer>
</body>
</html>
