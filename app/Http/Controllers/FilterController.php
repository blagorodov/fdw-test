<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureVoterUuid;
use App\Models\Car;
use App\Models\CarModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function models(Request $request): JsonResponse
    {
        $voterUuid = $request->session()->get(EnsureVoterUuid::SESSION_KEY);

        $items = CarModel::query()
            ->whereHas(
                'cars',
                fn ($query) => $query->whereDoesntHave(
                    'votes',
                    fn ($q) => $q->where('voter_uuid', $voterUuid),
                ),
                '>=',
                2,
            )
            ->with('carMark')
            ->get()
            ->sortBy(fn (CarModel $model): array => [$model->carMark->name, $model->name])
            ->values()
            ->map(fn (CarModel $model): array => [
                'id' => $model->id,
                'title' => $model->carMark->name.' '.$model->name,
            ]);

        return response()->json($items);
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
}
