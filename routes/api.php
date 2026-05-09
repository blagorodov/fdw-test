<?php

use App\Http\Controllers\FilterController;
use App\Http\Controllers\StatController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/models/all', [FilterController::class, 'modelsAll']);
Route::get('/models', [FilterController::class, 'models']);
Route::get('/years', [FilterController::class, 'years']);
Route::get('/stat', [StatController::class, 'stat']);
Route::get('/next/{car_model_id}', [VoteController::class, 'next']);
Route::post('/vote', [VoteController::class, 'vote']);
