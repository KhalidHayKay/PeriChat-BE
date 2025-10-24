<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GroupService;
use App\Services\MessageService;
use App\Http\Resources\UserResource;
use App\Http\Resources\GroupResource;
use App\Services\ConversationService;
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

    public function create(CreateConversationRequest $request, MessageService $messageService)
    {
        $data = $request->validated();

        $result = $this->service->createWithFirstMessage(
            $data,
            $request->user(),
            $messageService
        );

        return response()->json([
            'message' => 'Conversation created successfully',
            'data'    => $result,
        ], 201);
    }
}
