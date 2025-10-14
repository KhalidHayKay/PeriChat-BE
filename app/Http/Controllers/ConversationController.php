<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ConversationService;
use App\Http\Resources\ConversationSubjectResource;

class ConversationController extends Controller
{
    public function __construct(readonly protected ConversationService $service) {}

    public function index(Request $request)
    {
        $subjects = $this->service->subjects($request->user());

        return ConversationSubjectResource::collection($subjects);
    }
}
