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

        $group->users()->attach(array_unique($verData['members']));
        $group->users()->attach($user->id, ['role' => 'admin']);

        $conversation = Conversation::create(['group_id' => $group->id]);

        $subject = $this->constructSubject(
            $group,
            $conversation,
            "{$user->name} created this group"
        );

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

        $subject = $this->constructSubject(
            $group,
            $group->conversation,
            "You joined {$group->name}"
        );

        MemberJoined::dispatch($group, $user);

        return $subject;
    }

    private function constructSubject(Group $group, Conversation $conversation, string $message)
    {
        return (object) [
            'id'                            => $conversation->id,
            'name'                          => $group->name,
            'avatar'                        => $group->avatar,
            'type'                          => 'group',
            'type_id'                       => $group->id,
            'last_message'                  => $message,
            'last_message_attachment_count' => 0,
            'last_message_date'             => $group->created_at,
            'last_message_sender'           => 0,
            'unread_messages_count'         => 0,
        ];
    }
}
