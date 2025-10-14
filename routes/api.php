<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ConversationController;

Route::prefix('/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/social-login', [AuthController::class, 'socialLogin']);
    Route::post('/logout/{all?}', [AuthController::class, 'logout'])
        ->where('all', 'all');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('/conversation')->group(function () {
        Route::get('/subjects', [ConversationController::class, 'index']);
    });

    Route::prefix('/messaging/conversation/{conversation}')->group(function () {
        Route::get('', [MessageController::class, 'index']);
        Route::get('/older/{lastMessage}', [MessageController::class, 'older']);
        Route::post('/send', [MessageController::class, 'store']);
    });
});
