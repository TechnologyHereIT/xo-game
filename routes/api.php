<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatsController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/player/stats', [StatsController::class, 'getPlayerStats']);
});