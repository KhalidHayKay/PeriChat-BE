<?php

namespace App\Http\Controllers;

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
}
