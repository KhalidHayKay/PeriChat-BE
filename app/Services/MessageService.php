<?php

namespace App\Services;

use App\Models\User;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Str;
use App\Models\Conversation;
use App\Models\MessageAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MessageService
{
    public function getMessages(User $user, Conversation $conversation)
    {
        $this->terminateUnauthorized($user, $conversation);

        return $conversation->messages()
            ->with(['conversation', 'sender', 'attachments'])
            ->latest()
            ->simplePaginate(10);
    }

    public function getOlderMessages(User $user, Conversation $conversation, Message $lastMessage)
    {
        $this->terminateUnauthorized($user, $conversation);

        return $conversation->messages()
            ->with(['conversation', 'sender', 'attachments'])
            ->where('created_at', '<', $lastMessage->created_at)
            ->latest()
            ->simplePaginate(10);
    }

    public function store(array $verData, Conversation $conversation, User $user): Message
    {
        $verData['sender_id'] = $user->id;

        $files = $verData['attachments'] ?? null;
        unset($verData['attachments']);
        // dd($verData);
        $message = $conversation->messages()->create($verData);

        if ($files) {
            $attachments          = $this->handleAttachments($files, $message->id);
            $message->attachments = $attachments;
        }

        MessageSent::dispatch($message);

        return $message;
    }


    public function resetUnread(Conversation $conversation, User $user)
    {
        $this->resolveUnreadQuery($conversation, $user)
            ->update(['unread_messages_count' => 0]);
    }

    public function incrementUnreadSmart(Conversation $conversation, Message $message)
    {
        if ($message->receiver_id) {
            // Private message
            $receiver = User::findOrFail($message->receiver_id);
            $this->incrementUnread($conversation, $receiver);
        } else {
            // Group message
            $this->incrementUnreadForGroup($conversation, $message->sender_id);
        }
    }

    public function incrementUnread(Conversation $conversation, User $receiver)
    {
        $this->resolveUnreadQuery($conversation, $receiver)
            ->increment('unread_messages_count');
    }

    public function incrementUnreadForGroup(Conversation $conversation, int $senderId)
    {
        DB::table('group_user')
            ->where('group_id', $conversation->group_id)
            ->where('user_id', '!=', $senderId) // don’t increment for sender
            ->increment('unread_messages_count');
    }

    private function resolveUnreadQuery(Conversation $conversation, User $user)
    {
        $map = [
            'table' => $conversation->group_id ? 'group_user' : 'user_conversation',
            'foreign_key' => $conversation->group_id ? 'group_id' : 'conversation_id',
            'foreign_value' => $conversation->group_id ? $conversation->group_id : $conversation->id,
        ];

        return DB::table($map['table'])
            ->where($map['foreign_key'], $map['foreign_value'])
            ->where('user_id', $user->id);
    }

    private function handleAttachments(array $files, int $messageId): array
    {
        $attachments = [];

        foreach ($files as $file) {
            $dir = 'attachments/' . Str::random(32);
            Storage::makeDirectory($dir);

            $attachments[] = MessageAttachment::create([
                'message_id' => $messageId,
                'name'       => $file->getClientOriginalName(),
                'mime'       => $file->getClientMimeType(),
                'size'       => $file->getSize(),
                'path'       => $file->store($dir, 'public'),
            ]);
        }

        return $attachments;
    }

    private function terminateUnauthorized(User $user, Conversation $conversation)
    {
        if ($conversation->group_id) {
            if (!$conversation->group->users->contains($user->id)) {
                abort(403, 'Unauthorized access to this conversation.');
            }
        } elseif (!$conversation->users->contains($user->id)) {
            abort(403, 'Unauthorized access to this conversation.');
        }
    }
}
