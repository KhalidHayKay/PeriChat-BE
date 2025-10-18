<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\Conversation;

enum ConversationTypeEnum: string
{
    case PRIVATE = 'private';
    case GROUP   = 'group';

    public static function isPrivate(Conversation $conversation): bool
    {
        return $conversation->type === static::PRIVATE->value;
    }
}
