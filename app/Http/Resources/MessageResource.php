<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function PHPUnit\Framework\isArray;
use function PHPUnit\Framework\isEmpty;

class MessageResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'conversation' => $this->conversation_id,
            'message'     => $this->message ?? null,
            'senderId'    => (int) $this->sender_id,
            'receiverId'  => (int) $this->receiver_id,
            'groupId'     => $this->conversation->group_id,
            'sender'      => new UserResource($this->sender),
            'attachments' => collect($this->attachments)->isNotEmpty()
                ? MessageAttachmentResource::collection($this->attachments)
                : null,
            'createdAt'   => $this->created_at,
        ];
    }
}
