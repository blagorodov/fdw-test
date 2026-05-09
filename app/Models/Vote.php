<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['voter_uuid', 'car_id', 'is_selected'])]
class Vote extends Model
{
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    protected function casts(): array
    {
        return [
            'is_selected' => 'boolean',
        ];
    }
}
