<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use App\Services\GroupService;
use App\Http\Resources\GroupResource;

class GroupController extends Controller
{
    public function __construct(readonly protected GroupService $service) {}

    public function create(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'members'   => 'required|array|min:1',
            'members.*' => 'exists:users,id',
        ]);

        $data = $this->service->make($data, $request->user());

        return response()->json($data);
    }

    public function update(Request $request, Group $group)
    {
        $data = $request->validate([]);

        $group = $this->service->edit($data, $group);

        return GroupResource::make($group);
    }

    public function join(Request $request, Group $group)
    {
        $group = $this->service->join($group, $request->user());

        return GroupResource::make($group);
    }

    public function leave(Request $request)
    {
        //
    }
}
