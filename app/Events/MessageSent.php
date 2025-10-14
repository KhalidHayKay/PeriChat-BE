<?php

namespace App\Events;

use App\Enums\ConversationTypeEnum;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public readonly Message $message)
    {
        //
    }

    public function broadcastWith(): array
    {
        return [
            'message' => new MessageResource($this->message),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $m        = $this->message;
        $channels = [];

        if ($m->conversation->type === ConversationTypeEnum::GROUP->value) {
            $channels[] = new PrivateChannel('message.group.' . $m->conversation->group_id);
        } else {
            $channels[] = new PrivateChannel('message.private.' . collect([$m->sender_id, $m->receiver_id])
                ->sort()->implode('-'));
        }

        return $channels;
    }
}
