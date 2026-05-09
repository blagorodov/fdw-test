<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased min-h-screen flex items-center justify-center bg-gray-100">
        <div class="flex flex-wrap gap-4 justify-center">
            <a
                href="{{ route('vote') }}"
                class="rounded-lg bg-blue-600 px-6 py-3 text-white shadow hover:bg-blue-700"
            >
                Голосовать
            </a>
            <a
                href="{{ route('stat') }}"
                class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-gray-800 shadow-sm hover:bg-gray-50"
            >
                Статистика
            </a>
        </div>
    </body>
</html>
