<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureVoterUuid;
use App\Http\Requests\StoreVoteRequest;
use App\Models\Car;
use App\Models\Vote;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VoteController extends Controller
{
    public function next(Request $request, string $car_model_id): JsonResponse
    {
        $voterUuid = $request->session()->get(EnsureVoterUuid::SESSION_KEY);

        $cars = Car::query()
            ->where('car_model_id', $car_model_id)
            ->whereDoesntHave('votes', function ($query) use ($voterUuid): void {
                $query->where('voter_uuid', $voterUuid);
            })
            ->inRandomOrder()
            ->limit(2)
            ->get();

        if ($cars->count() < 2) {
            abort(404);
        }

        $payload = $cars->map(fn (Car $car): array => [
            'id' => $car->id,
            'image' => Storage::disk('public')->url('cars/'.$car->image),
        ])->all();

        return response()->json($payload);
    }

    public function vote(StoreVoteRequest $request): Response
    {
        $validated = $request->validated();

        $selectedId = (int) $validated['selected_car_id'];
        $otherId = (int) $validated['other_car_id'];
        $carIds = [$selectedId, $otherId];
        sort($carIds);

        $voterUuid = $request->session()->get(EnsureVoterUuid::SESSION_KEY);

        try {
            DB::transaction(function () use ($voterUuid, $selectedId, $otherId, $carIds): void {
                $cars = Car::query()
                    ->whereIn('id', $carIds)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                if ($cars->count() !== 2) {
                    abort(404);
                }

                if ($cars->pluck('car_model_id')->unique()->count() !== 1) {
                    abort(422, 'Машины должны относиться к одной модели');
                }

                $alreadyVoted = Vote::query()
                    ->where('voter_uuid', $voterUuid)
                    ->whereIn('car_id', [$selectedId, $otherId])
                    ->exists();

                if ($alreadyVoted) {
                    abort(409);
                }

                Vote::query()->create([
                    'voter_uuid' => $voterUuid,
                    'car_id' => $selectedId,
                    'is_selected' => true,
                ]);
                Vote::query()->create([
                    'voter_uuid' => $voterUuid,
                    'car_id' => $otherId,
                    'is_selected' => false,
                ]);
            });
        } catch (UniqueConstraintViolationException) {
            abort(409);
        }

        return response()->noContent();
    }
}
