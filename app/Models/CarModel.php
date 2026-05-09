<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['car_mark_id', 'name'])]
class CarModel extends Model
{
    public $timestamps = false;

    public function carMark(): BelongsTo
    {
        return $this->belongsTo(CarMark::class);
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
