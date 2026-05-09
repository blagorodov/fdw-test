<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['car_model_id', 'year', 'image'])]
class Car extends Model
{
    public $timestamps = false;

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}
