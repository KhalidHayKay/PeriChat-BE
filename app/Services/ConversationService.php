<?php

namespace App\Services;

use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    public function subjects(User $user)
    {
        $privateConversations = $this->getPrivateSubjects($user);
        $groupConversations   = $this->getGroupSubjects($user);

        $result = $privateConversations
            ->unionAll($groupConversations)
            ->orderByDesc('last_message_date')
            ->orderBy('name')
            ->get();

        return $result;
    }

    public static function users(User $user)
    {
        $result = User::where('id', '!=', $user->id)
            ->whereNotExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('user_conversation as uc1')
                    ->join(
                        'user_conversation as uc2',
                        'uc2.conversation_id',
                        '=',
                        'uc1.conversation_id'
                    )
                    ->whereRaw('uc1.user_id = users.id')
                    ->where('uc2.user_id', '=', $user->id);
            })
            ->orderBy('name')->get();

        return $result;
    }

    public static function groups(User $user)
    {
        $result = Group::where('is_private', false)
            ->whereNotExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('group_user')
                    ->where('group_user.group_id', '=', 'groups.id')
                    ->where('group_user.user_id', '=', $user->id);
            })
            ->with('users:id')
            ->orderBy('name')
            ->get();

        return $result;
    }

    public static function groupUsers(User $user)
    {
        $result = User::where('users.id', '!=', $user->id)
            ->leftJoin('user_conversation as uc', 'uc.user_id', '=', 'users.id')
            ->leftJoin('conversations as c', 'c.id', '=', 'uc.conversation_id')
            ->leftJoin('messages as m', 'm.id', '=', 'c.last_message_id')
            ->where(function ($query) use ($user) {
                $query->where('m.sender_id', '=', $user->id)
                    ->orWhere('m.receiver_id', '=', $user->id);
            })
            ->orderBy('m.created_at', 'desc')
            ->orderBy('users.name')
            ->get();

        return $result;
    }

    private function getPrivateSubjects(User $user)
    {
        return DB::table('users as u')
            ->select([
                'u.id as type_id',
                'u.name',
                'u.avatar',
                'c.id as id',
                'm.message as last_message',
                'm.sender_id as last_message_sender',
                'm.created_at as last_message_date',
                'uc_current.unread_messages_count',
                DB::raw('(SELECT COUNT(*) FROM message_attachments WHERE message_attachments.message_id = m.id) as last_message_attachment_count'),
                DB::raw("'private' as type"),
            ])
            ->where('u.id', '!=', $user->id)
            ->join('user_conversation as uc', 'uc.user_id', '=', 'u.id')
            ->join('conversations as c', 'c.id', '=', 'uc.conversation_id')
            ->join('user_conversation as uc_current', function ($join) use ($user) {
                $join->on('uc_current.conversation_id', '=', 'c.id')
                    ->where('uc_current.user_id', '=', $user->id);
            })
            ->leftJoin('messages as m', 'm.id', '=', 'c.last_message_id')
            ->whereNull('c.group_id')
            ->where(function ($query) use ($user) {
                $query->where('m.sender_id', '=', $user->id)
                    ->orWhere('m.receiver_id', '=', $user->id);
            });
    }

    private function getGroupSubjects(User $user)
    {
        return DB::table('groups as g')
            ->select([
                'g.id as type_id',
                'g.name',
                'g.avatar',
                'c.id as id',
                'm.message as last_message',
                'm.sender_id as last_message_sender',
                'm.created_at as last_message_date',
                'gu.unread_messages_count',
                DB::raw('(SELECT COUNT(*) FROM message_attachments WHERE message_attachments.message_id = m.id) as last_message_attachment_count'),
                DB::raw("'group' as type"),
            ])
            ->join('group_user as gu', 'gu.group_id', '=', 'g.id')
            ->leftJoin('conversations as c', 'c.group_id', '=', 'g.id')
            ->leftJoin('messages as m', 'm.id', '=', 'c.last_message_id')
            ->where('gu.user_id', '=', $user->id)
            ->whereNotNull('c.group_id');
    }
}
