<?php

declare(strict_types=1);

namespace App\Enums;

enum GroupRoleEnum: string
{
    case ADMIN  = 'admin';
    case MEMBER = 'member';
}
