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

        $subject = $this->constructSubject($group, $user, $conversation);
        GroupCreated::dispatch($group, $subject);

        return $subject;
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

    private function constructSubject(Group $group, User $user, Conversation $conversation)
    {
        return (object) [
            'id'                            => $conversation->id,
            'name'                          => $group->name,
            'avatar'                        => $group->avatar,
            'type'                          => 'group',
            'type_id'                       => $group->id,
            'last_message'                  => "{$user->name} created this group",
            'last_message_attachment_count' => 0,
            'last_message_date'             => $group->created_at,
            'last_message_sender'           => 0,
            'unread_messages_count'         => 0,
        ];
    }
}
