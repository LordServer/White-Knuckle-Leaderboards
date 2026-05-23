<?php

namespace App\Enum;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case BANNED = 'banned';
    case SUSPENDED = 'suspended';
}
