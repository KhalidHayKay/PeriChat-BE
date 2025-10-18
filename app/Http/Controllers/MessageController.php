<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Services\MessageService;
use App\Http\Resources\MessageResource;
use App\Http\Requests\StoreMessageRequest;

class MessageController extends Controller
{
    public function __construct(readonly protected MessageService $service) {}

    public function index(Request $request, Conversation $conversation)
    {
        $messages = $this->service->getMessages($request->user(), $conversation);

        return MessageResource::collection($messages);
    }

    public function older(Request $request, Conversation $conversation, Message $lastMessage)
    {
        $messages = $this->service->getOlderMessages($request->user(), $conversation, $lastMessage);

        return MessageResource::collection($messages);
    }

    public function store(StoreMessageRequest $request, Conversation $conversation)
    {
        $data = $request->validated();

        $message = $this->service->store($data, $conversation, $request->user());

        return new MessageResource($message);
    }

    public function markAsRead(Request $request, Conversation $conversation)
    {
        $this->service->resetUnread($conversation, $request->user());

        return response()->json([
            'status' => 'success',
            'message' => 'Conversation marked as read.'
        ]);
    }

    public function incrementUnread(Request $request, Conversation $conversation, Message $message)
    {
        $this->service->incrementUnreadSmart($conversation, $message);

        return response()->json([
            'status' => 'success',
            'message' => 'Unread count incremented successfully.',
        ]);
    }
}
