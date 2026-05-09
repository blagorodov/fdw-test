<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Статистика — {{ config('app.name', 'Laravel') }}</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="relative min-h-screen bg-gray-100 antialiased">
        <a
            href="{{ route('welcome') }}"
            class="absolute left-4 top-4 rounded-lg border border-gray-300 bg-white px-6 py-3 text-gray-800 shadow-sm hover:bg-gray-50"
        >
            Назад
        </a>
    </body>
</html>
