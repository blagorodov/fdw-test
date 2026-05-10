<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatRequest;
use App\Models\Car;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class StatController extends Controller
{
    public function stat(StatRequest $request): JsonResponse
    {
        $validated = $request->validated();

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
}
