<?php

use App\Http\Resources\UserResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('online', function (User $user) {
    return $user ? new UserResource($user) : null;
});

Broadcast::channel(
    'message.private.{user1Id}-{user2Id}',
    fn(User $user, int $user1Id, int $user2Id) => $user->id === $user1Id || $user->id === $user2Id ? $user : null
);

Broadcast::channel(
    'message.group.{groupId}',
    fn(User $user, int $groupId) => $user->groups->contains('id', $groupId) ? $user : null
);
