<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

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

        <main class="flex min-h-screen flex-col items-center justify-start gap-6 px-4 pt-16">
            <h1 class="text-3xl font-bold text-gray-900">Голосование</h1>

            <div class="flex w-full max-w-2xl flex-col gap-2">
                <label for="vote-model-select" class="text-sm font-medium text-gray-700">
                    Модель автомобиля
                </label>
                <select
                    id="vote-model-select"
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                >
                    <option value="">Выберите модель</option>
                </select>
                <p id="vote-models-error" class="hidden text-sm text-red-600"></p>
            </div>

            <p
                id="vote-pair-message"
                class="hidden max-w-xl text-center text-gray-700"
            ></p>

            <p
                id="vote-pair-instruction"
                class="hidden max-w-xl text-center text-lg font-medium text-gray-800"
            >
                Выберите понравившееся изображение
            </p>

            <div id="vote-pair" class="hidden flex flex-wrap items-start justify-center gap-8">
                <div class="vote-img-wrap cursor-pointer rounded-lg border border-gray-200 bg-white p-2 shadow-sm">
                    <img
                        id="vote-img-a"
                        src=""
                        alt=""
                        class="max-h-72 max-w-full object-contain"
                    />
                </div>
                <div class="vote-img-wrap cursor-pointer rounded-lg border border-gray-200 bg-white p-2 shadow-sm">
                    <img
                        id="vote-img-b"
                        src=""
                        alt=""
                        class="max-h-72 max-w-full object-contain"
                    />
                </div>
            </div>
        </main>
    </body>
</html>
