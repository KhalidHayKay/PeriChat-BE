<?php

use App\Models\User;
use App\Models\Conversation;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('online', function (User $user) {
    return $user ? new UserResource($user) : null;
});

Broadcast::channel('conversation.{conversation}', function (User $user, Conversation $conversation) {
    if ($conversation->group_id) {
        return $user->groups()->where('groups.id', $conversation->group_id)->exists();
    }

    return $conversation->users()->where('users.id', $user->id)->exists();
});

Broadcast::channel('user.{userId}', function (User $user, int $userId) {
    return (int) $user->id === (int) $userId;
});
