<?php

use App\Http\Controllers\ViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ViewController::class, 'welcome'])->name('welcome');

Route::get('/vote', [ViewController::class, 'vote'])->name('vote');

Route::get('/stat', [ViewController::class, 'stat'])->name('stat');
