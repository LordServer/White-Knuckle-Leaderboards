<?php

namespace App\Enum;

enum UserRole: string
{
    case USER = 'ROLE_USER';
    case AUTHORIZER = 'ROLE_AUTHORIZER';
    case MODERATOR = 'ROLE_MODERATOR';
    case ADMIN = 'ROLE_ADMIN';
}
