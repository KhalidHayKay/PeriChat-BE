<?php

namespace App\Events;

use App\Models\Group;
use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use App\Http\Resources\GroupResource;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Http\Resources\ConversationSubjectResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class GroupCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Group $group,
        public readonly object $conversationSubject,
    ) {
        //
    }

    public function broadcastWith(): array
    {
        return [
            'conversation' => ConversationSubjectResource::make($this->conversationSubject),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return $this->group->users()
            // ->where('user_id', '!=', $this->group->owner_id)
            ->get()
            ->map(fn ($user) => new PrivateChannel("user.{$user->id}"))
            ->toArray();
    }
}
