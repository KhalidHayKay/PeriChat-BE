<?php

namespace App\Services;

use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Str;
use App\Models\Conversation;
use App\Events\MessageSent;
use App\Models\MessageAttachment;
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
            ->where('id', '<', $lastMessage->id)
            ->latest()
            ->simplePaginate(10);
    }

    public function store(array $verData, Conversation $conversation, User $user): Message
    {
        $verData['sender_id'] = $user->id;
        $verData['read_at']   = now();

        $files = $verData['attachments'] ?? null;
        unset($verData['attachments']);

        $message = $conversation->messages()->create($verData);

        if ($files) {
            $attachments          = $this->handleAttachments($files, $message->id);
            $message->attachments = $attachments;
        }

        MessageSent::dispatch($message);

        return $message;
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
