<?php

namespace App\Enum;

enum ApiScopes: string
{
    case SCOPE_USER_UPDATE = 'ROLE_USER_UPDATE';
    case SCOPE_CLIMB_CREATE = 'ROLE_CLIMB_CREATE';
    case SCOPE_CLIMB_UPDATE = 'ROLE_CLIMB_UPDATE';

    public const SCOPES = [
        ApiScopes::SCOPE_USER_UPDATE->value => 'Update User',
        ApiScopes::SCOPE_CLIMB_CREATE->value => 'Create Climbs',
        ApiScopes::SCOPE_CLIMB_UPDATE->value => 'Update Climbs',
    ];
}
