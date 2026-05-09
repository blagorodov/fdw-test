<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class CarMark extends Model
{
    public $timestamps = false;

    public function carModels(): HasMany
    {
        return $this->hasMany(CarModel::class);
    }
}
