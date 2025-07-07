<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\TopicController;

Route::get('/habits', [HabitController::class, 'index']);
Route::post('/habits', [HabitController::class, 'store']);
Route::delete('/habits/{habit}', [HabitController::class, 'destroy']);
Route::patch('/habits/{habit}/toggle', [HabitController::class, 'toggle']);

Route::get('/api/v1/topics', [TopicController::class, 'index']);
