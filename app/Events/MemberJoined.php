<?php

namespace App\Events;

use App\Models\User;
use App\Models\Group;
use App\Http\Resources\UserResource;
use Illuminate\Broadcasting\Channel;
use App\Http\Resources\GroupResource;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MemberJoined implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Group $group,
        public readonly User $user,
    ) {
        //
    }

    public function broadcastWith(): array
    {
        return [
            'group'  => GroupResource::make($this->group),
            'member' => UserResource::make($this->user),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("conversation.{$this->group->conversation->id}"),
        ];
    }
}
