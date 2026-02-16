<?php

use Illuminate\Http\Request;
use App\Http\Middleware\CacheResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Schedule;
use App\Http\Controllers\TaskController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum', CacheResponse::class])->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // Весь CRUD задач теперь под кэшем (GET) и авто-очисткой (POST/PUT/DELETE)
    Route::apiResource('tasks', TaskController::class);
});

Schedule::command('app:check-overdue-tasks')->everyMinute();
