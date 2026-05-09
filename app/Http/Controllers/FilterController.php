<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureVoterUuid;
use App\Models\Car;
use App\Models\CarModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FilterController extends Controller
{
    public function models(Request $request): JsonResponse
    {
        $voterUuid = $request->session()->get(EnsureVoterUuid::SESSION_KEY);

        return response()->json($this->buildModelsPayload($voterUuid));
    }

    public function modelsAll(): JsonResponse
    {
        return response()->json($this->buildModelsPayload(null));
    }

    public function stat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'car_model_id' => ['required', 'integer', Rule::exists('car_models', 'id')],
            'year_from' => ['required', 'integer', Rule::exists('cars', 'year')],
            'year_to' => ['required', 'integer', Rule::exists('cars', 'year')],
        ], [
            'car_model_id.exists' => 'Модель не существует',
            'year_from.exists' => 'Год не существует',
            'year_to.exists' => 'Год не существует',
        ]);

        if ($validated['year_from'] > $validated['year_to']) {
            throw ValidationException::withMessages([
                'year_from' => ['Год от должен быть не больше Года до'],
            ]);
        }

        $cars = Car::query()
            ->where('car_model_id', $validated['car_model_id'])
            ->whereBetween('year', [$validated['year_from'], $validated['year_to']])
            ->withCount(['votes as votes_count' => fn ($q) => $q->where('is_selected', true)])
            ->orderByDesc('votes_count')
            ->get();

        if ($cars->isEmpty()) {
            abort(404);
        }

        $payload = $cars->map(fn (Car $car): array => [
            'id' => $car->id,
            'image' => Storage::disk('public')->url('cars/'.$car->image),
            'year' => $car->year,
            'votes_count' => $car->votes_count,
        ])->all();

        return response()->json($payload);
    }

    public function years(): JsonResponse
    {
        $years = Car::query()
            ->select('year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year');

        return response()->json($years->values()->all());
    }

    /**
     * @return array<int, array{id: int, title: string}>
     */
    private function buildModelsPayload(?string $voterUuid): array
    {
        $query = CarModel::query()->with('carMark');

        if ($voterUuid !== null) {
            $query->whereHas(
                'cars',
                fn ($query) => $query->whereDoesntHave(
                    'votes',
                    fn ($q) => $q->where('voter_uuid', $voterUuid),
                ),
                '>=',
                2,
            );
        } else {
            $query->whereHas('cars', fn ($q) => $q, '>=', 2);
        }

        return $query->get()
            ->sortBy(fn (CarModel $model): array => [$model->carMark->name, $model->name])
            ->values()
            ->map(fn (CarModel $model): array => [
                'id' => $model->id,
                'title' => $model->carMark->name.' '.$model->name,
            ])
            ->all();
    }
}
