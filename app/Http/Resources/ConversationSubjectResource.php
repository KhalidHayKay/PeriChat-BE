<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationSubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                         => $this->id,
            'name'                       => $this->name,
            'avatar'                     => $this->avatar,
            'type'                       => $this->type,
            'typeId'                     => $this->type_id,
            'lastMessage'                => $this->last_message,
            'lastMessageAttachmentCount' => $this->last_message_attachment_count,
            'lastMessageDate'            => $this->last_message_date,
            'lastMessageSenderId'        => $this->last_message_sender,
            'unreadMessageCount'         => $this->unread_messages_count,
        ];
    }
}
