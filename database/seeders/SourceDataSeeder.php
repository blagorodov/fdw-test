<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarMark;
use App\Models\CarModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;

/**
 * Imports cars from JSON files in source-data/. Assumes no related votes rows for cars.
 */
class SourceDataSeeder extends Seeder
{
    private const SOURCE_DIR = 'source-data';

    public function run(): void
    {
        if (Car::query()->exists()) {
            $this->command->warn('Таблица cars уже содержит записи — сидер пропущен.');

            return;
        }

        $jsonPaths = $this->collectJsonPaths();
        $total = count($jsonPaths);

        $this->command->info("Найдено JSON-файлов (машин): {$total}");

        if ($total === 0) {
            $this->command->warn('В '.base_path(self::SOURCE_DIR).' нет *.json — нечего импортировать.');

            return;
        }

        $destinationDir = storage_path('app/public/cars');
        File::ensureDirectoryExists($destinationDir);

        $copiedPaths = [];
        $pendingCars = [];

        $progress = $this->command->getOutput()->createProgressBar($total);
        $progress->start();

        try {
            foreach ($jsonPaths as $jsonPath) {
                $payload = $this->decodeJsonFile($jsonPath);

                $year = $payload['Year'] ?? null;
                $make = $payload['Make'] ?? null;
                $modelName = $payload['Model'] ?? null;
                $imageName = $payload['Image'] ?? null;

                if ($year === null || $make === null || $modelName === null || $imageName === null) {
                    throw new RuntimeException(
                        'В файле отсутствуют обязательные поля Year, Make, Model или Image: '.$jsonPath
                    );
                }

                $mark = CarMark::firstOrCreate(['name' => $make]);

                $carModel = CarModel::firstOrCreate([
                    'car_mark_id' => $mark->id,
                    'name' => $modelName,
                ]);

                $sourceImagePath = base_path(self::SOURCE_DIR.'/'.$imageName);
                if (! File::exists($sourceImagePath)) {
                    throw new RuntimeException(
                        "Файл изображения не найден: {$imageName} (JSON: {$jsonPath})"
                    );
                }

                $destinationPath = $destinationDir.'/'.$imageName;
                File::copy($sourceImagePath, $destinationPath);
                $copiedPaths[] = $destinationPath;

                $pendingCars[] = [
                    'car_model_id' => $carModel->id,
                    'year' => (int) $year,
                    'image' => $imageName,
                ];

                $progress->advance();
            }

            $progress->finish();
            $this->command->newLine();

            DB::transaction(function () use ($pendingCars): void {
                foreach ($pendingCars as $row) {
                    Car::create($row);
                }
            });

            $this->command->info("Готово: импортировано машин: {$total}.");
        } catch (Throwable $e) {
            $progress->finish();
            $this->command->newLine();

            $this->rollbackImport($copiedPaths);

            $this->command->error('Импорт прерван: '.$e->getMessage());

            throw $e;
        }
    }

    private function collectJsonPaths(): array
    {
        $dir = base_path(self::SOURCE_DIR);
        if (! File::isDirectory($dir)) {
            return [];
        }

        $paths = File::glob($dir.'/*.json') ?: [];

        sort($paths, SORT_STRING);

        return $paths;
    }

    private function decodeJsonFile(string $path): array
    {
        $raw = File::get($path);
        $data = json_decode($raw, true);

        if (! is_array($data)) {
            throw new RuntimeException('Некорректный JSON: '.$path);
        }

        return $data;
    }

    private function rollbackImport(array $copiedPaths): void
    {
        foreach ($copiedPaths as $path) {
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        Car::query()->delete();

        $this->command->warn('Откат: удалены скопированные изображения и все строки из таблицы cars.');
    }
}
