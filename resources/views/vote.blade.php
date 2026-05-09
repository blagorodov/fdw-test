<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Голосование — {{ config('app.name', 'Laravel') }}</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/vote.js'])
    </head>
    <body class="relative min-h-screen bg-gray-100 antialiased">
        <a
            href="{{ route('welcome') }}"
            class="absolute left-4 top-4 rounded-lg border border-gray-300 bg-white px-6 py-3 text-gray-800 shadow-sm hover:bg-gray-50"
        >
            Назад
        </a>

        <main class="flex min-h-screen flex-col items-center justify-center gap-4 px-4 pt-16">
            <p
                id="jq-status"
                class="hidden rounded-lg border border-green-200 bg-green-50 px-4 py-2 text-green-900"
            ></p>
            <button
                type="button"
                id="jq-check-btn"
                class="rounded-lg bg-blue-600 px-6 py-3 text-white shadow hover:bg-blue-700"
            >
                Проверить jQuery
            </button>
            <p id="jq-click-msg" class="min-h-[1.5rem] text-sm text-gray-600"></p>
        </main>
    </body>
</html>
