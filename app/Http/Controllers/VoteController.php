<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureVoterUuid;
use App\Models\Car;
use App\Models\Vote;
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
            ->orderBy('id')
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

    public function vote(Request $request): Response
    {
        $validated = $request->validate([
            'selected_car_id' => ['required', 'integer'],
            'other_car_id' => ['required', 'integer', 'different:selected_car_id'],
        ]);

        $selectedId = (int) $validated['selected_car_id'];
        $otherId = (int) $validated['other_car_id'];

        $cars = Car::query()->whereIn('id', [$selectedId, $otherId])->get();
        if ($cars->count() !== 2) {
            abort(404);
        }

        $voterUuid = $request->session()->get(EnsureVoterUuid::SESSION_KEY);

        $alreadyVoted = Vote::query()
            ->where('voter_uuid', $voterUuid)
            ->whereIn('car_id', [$selectedId, $otherId])
            ->exists();

        if ($alreadyVoted) {
            abort(409);
        }

        DB::transaction(function () use ($voterUuid, $selectedId, $otherId): void {
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

        return response()->noContent();
    }
}
