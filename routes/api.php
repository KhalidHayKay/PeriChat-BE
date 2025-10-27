<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ConversationController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

Route::prefix('/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout/{all?}', [AuthController::class, 'logout'])
        ->where('all', 'all');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('/conversation')->group(function () {
        Route::get('/subjects', [ConversationController::class, 'index']);
        Route::get('/new/users', [ConversationController::class, 'users']);
        Route::get('/new/groups', [ConversationController::class, 'groups']);
        Route::get('/new/group-users', [ConversationController::class, 'groupUsers']);
        Route::post('/create/private/{user}', [ConversationController::class, 'create']);
    });

    Route::prefix('/group')->group(function () {
        Route::post('/new', [GroupController::class, 'create']);
        Route::post('{group}/join', [GroupController::class, 'join']);
        Route::post('{group}/leave', [GroupController::class, 'leave']);
        // need to add middleware to check user has priviledge to perform group admin
        Route::patch('/update', [GroupController::class, 'update']);
    });

    Route::prefix('/messaging/conversation/{conversation}')->group(function () {
        Route::get('', [MessageController::class, 'index']);
        Route::get('/older/{lastMessage}', [MessageController::class, 'older']);
        Route::post('/send', [MessageController::class, 'store']);

        Route::post('/unread/reset', [MessageController::class, 'markAsRead']);
        Route::post('message/{message}/unread/increment', [MessageController::class, 'incrementUnread']);
    });
});
