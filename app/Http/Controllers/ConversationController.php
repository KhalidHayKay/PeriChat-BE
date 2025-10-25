<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\GroupService;
use App\Services\MessageService;
use App\Http\Resources\UserResource;
use App\Http\Resources\GroupResource;
use App\Services\ConversationService;
use App\Http\Resources\MessageResource;
use App\Http\Controllers\GroupController;
use App\Http\Requests\CreateConversationRequest;
use App\Http\Resources\ConversationSubjectResource;

class ConversationController extends Controller
{
    public function __construct(readonly protected ConversationService $service) {}

    public function index(Request $request)
    {
        $subjects = $this->service->subjects($request->user());

        return ConversationSubjectResource::collection($subjects);
    }

    public function groups(Request $request)
    {
        $data = $this->service->groups($request->user());

        return GroupResource::collection($data);
    }

    public function users(Request $request)
    {
        $data = $this->service->users($request->user());

        return UserResource::collection($data);
    }

    public function groupUsers(Request $request)
    {
        $data = $this->service->groupusers($request->user());

        return UserResource::collection($data);
    }

    public function create(CreateConversationRequest $request, User $user, MessageService $messageService)
    {
        $data = $request->validated();

        [$subject, $message] = $this->service->createWithFirstMessage(
            $request->user(),
            $user,
            $data,
            $messageService
        );

        return response()->json([
            'message' => 'Conversation created successfully',
            'data'    => [
                'conversation' => ConversationSubjectResource::make($subject),
                'message'      => MessageResource::make($message),
            ],
        ], 201);
    }
}
