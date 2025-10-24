<?php

namespace App\Services;

use App\Models\User;
use App\Models\Group;
use App\Events\GroupCreated;
use App\Events\MemberJoined;
use App\Models\Conversation;

class GroupService
{
    public function make(array $verData, User $user)
    {
        $group = Group::create([
            'name'     => $verData['name'],
            'owner_id' => $user->id,
        ]);

        $group->users()->attach(array_unique([...$verData['members'], $user]));

        $conversation = Conversation::create(['group_id' => $group->id]);

        GroupCreated::dispatch($group, $conversation);

        return ['group' => $group, 'conversation' => $conversation];
    }

    public function edit(array $data, Group $group)
    {
        $group = $group->update($data);

        return $group;
    }

    public function join(Group $group, User $user)
    {
        $group->users()->attach($user);

        MemberJoined::dispatch($group, $user);

        return $group;
    }
}
