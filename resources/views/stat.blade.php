<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Статистика — {{ config('app.name', 'Laravel') }}</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/stat.js'])
    </head>
    <body class="relative min-h-screen bg-gray-100 antialiased">
        <a
            href="{{ route('welcome') }}"
            class="absolute left-4 top-4 rounded-lg border border-gray-300 bg-white px-6 py-3 text-gray-800 shadow-sm hover:bg-gray-50"
        >
            Назад
        </a>

        <main class="flex min-h-screen w-full flex-col items-center justify-start gap-6 px-4 pt-16 pb-12">
            <h1 class="text-3xl font-bold text-gray-900">Статистика</h1>

            <div class="flex w-full max-w-2xl flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label for="stat-model-select" class="text-sm font-medium text-gray-700">
                        Модель автомобиля
                    </label>
                    <select
                        id="stat-model-select"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                    >
                        <option value="">Выберите модель</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="stat-year-from" class="text-sm font-medium text-gray-700">
                            Год от
                        </label>
                        <select
                            id="stat-year-from"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                        >
                            <option value="">Выберите год</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="stat-year-to" class="text-sm font-medium text-gray-700">
                            Год до
                        </label>
                        <select
                            id="stat-year-to"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                        >
                            <option value="">Выберите год</option>
                        </select>
                    </div>
                </div>

                <p id="stat-filters-error" class="hidden text-sm text-red-600"></p>
            </div>

            <div class="w-full max-w-5xl overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                <table id="stat-table" class="display cell-border w-full min-w-[20rem] text-left text-sm text-gray-900">
                    <thead>
                        <tr>
                            <th>Фото</th>
                            <th>Данные</th>
                            <th>Голосов</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </main>
    </body>
</html>
