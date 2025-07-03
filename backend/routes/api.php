<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HabitController;

Route::get('/habits', [HabitController::class, 'index']);
Route::post('/habits', [HabitController::class, 'store']);
Route::delete('/habits/{habit}', [HabitController::class, 'destroy']);
Route::patch('/habits/{habit}/toggle', [HabitController::class, 'toggle']);
