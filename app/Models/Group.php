<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'avatar',
        'is_private',
        'owner_id',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getUserGroups(User $user): Collection
    {
        $query = self::select([
            'groups.*',
            'c.id as c_id',
            'm.message as last_message',
            'm.sender_id as last_message_sender',
            'm.created_at as last_message_date',
            DB::raw('(SELECT COUNT(*) FROM message_attachments WHERE message_attachments.message_id = m.id) as last_message_attachment_count'),
        ])
            ->leftJoin('group_user as gu', 'gu.group_id', '=', 'groups.id')
            ->leftJoin('conversations as c', 'c.group_id', '=', 'groups.id')
            ->leftJoin('messages as m', 'm.id', '=', 'c.last_message_id')
            ->where(function ($query) use ($user) {
                $query->where('gu.user_id', '=', $user->id)
                    // ->orWhere('groups.is_private', '!=', true)
                ;
            })
            ->distinct()
            ->orderBy('m.created_at', 'desc')
            ->orderBy('groups.name');

        return $query->get();
    }

    public function getUnreadCount()
    {
        $query = DB::table('group_user')
            ->select('unread_messages_count')
            ->where('group_id', $this->id)
            ->where('user_id', Auth::id());

        return $query->first()?->unread_messages_count;
    }
}
