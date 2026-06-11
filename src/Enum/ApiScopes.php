<?php

namespace App\Enum;

enum ApiScopes: string
{
    case SCOPE_APPROVALS_READ = 'ROLE_APPROVALS_READ';
    case SCOPE_CATEGORY_CREATE = 'ROLE_CATEGORY_CREATE';
    case SCOPE_CATEGORY_DELETE = 'ROLE_CATEGORY_DELETE';
    case SCOPE_CATEGORY_READ = 'ROLE_CATEGORY_READ';
    case SCOPE_CATEGORY_UPDATE = 'ROLE_CATEGORY_UPDATE';
    case SCOPE_CLIMB_CREATE = 'ROLE_CLIMB_CREATE';
    case SCOPE_CLIMB_READ = 'ROLE_CLIMB_READ';
    case SCOPE_CLIMB_REVIEW = 'ROLE_CLIMB_REVIEW';
    case SCOPE_MODERATE_USER = 'ROLE_MODERATE_USER';
    case SCOPE_OWNED_CLIMB_DELETE = 'ROLE_OWNED_CLIMB_DELETE';
    case SCOPE_OWNED_CLIMB_UPDATE = 'ROLE_OWNED_CLIMB_UPDATE';
    case SCOPE_RANK_METHOD_CREATE = 'ROLE_RANK_METHOD_CREATE';
    case SCOPE_RANK_METHOD_DELETE = 'ROLE_RANK_METHOD_DELETE';
    case SCOPE_RANK_METHOD_READ = 'ROLE_RANK_METHOD_READ';
    case SCOPE_RANK_METHOD_UPDATE = 'ROLE_RANK_METHOD_UPDATE';
    case SCOPE_SELF_USER_DELETE = 'ROLE_SELF_USER_DELETE';
    case SCOPE_SELF_USER_UPDATE = 'ROLE_SELF_USER_UPDATE';
    case SCOPE_SUBCATEGORY_CREATE = 'ROLE_SUBCATEGORY_CREATE';
    case SCOPE_SUBCATEGORY_DELETE = 'ROLE_SUBCATEGORY_DELETE';
    case SCOPE_SUBCATEGORY_READ = 'ROLE_SUBCATEGORY_READ';
    case SCOPE_SUBCATEGORY_UPDATE = 'ROLE_SUBCATEGORY_UPDATE';
    case SCOPE_UNOWNED_CLIMB_DELETE = 'ROLE_UNOWNED_CLIMB_DELETE';
    case SCOPE_UNOWNED_CLIMB_UPDATE = 'ROLE_UNOWNED_CLIMB_UPDATE';
    case SCOPE_USER_CREATE = 'ROLE_USER_CREATE';
    case SCOPE_USER_DELETE = 'ROLE_USER_DELETE';
    case SCOPE_USER_READ = 'ROLE_USER_READ';
    case SCOPE_USER_UPDATE = 'ROLE_USER_UPDATE';

    public const SCOPES = [
        ApiScopes::SCOPE_APPROVALS_READ->value => 'View all pending approvals',
        ApiScopes::SCOPE_CATEGORY_CREATE->value => 'Create a new category',
        ApiScopes::SCOPE_CATEGORY_DELETE->value => 'Delete an existing category',
        ApiScopes::SCOPE_CATEGORY_READ->value => 'View an existing category',
        ApiScopes::SCOPE_CATEGORY_UPDATE->value => 'Update and existing category',
        ApiScopes::SCOPE_CLIMB_CREATE->value => 'Submit a new climb',
        ApiScopes::SCOPE_CLIMB_READ->value => 'View an existing climb',
        ApiScopes::SCOPE_CLIMB_REVIEW->value => 'Review a climb',
        ApiScopes::SCOPE_MODERATE_USER->value => 'Moderate a user',
        ApiScopes::SCOPE_OWNED_CLIMB_DELETE->value => 'Delete a climb you submitted',
        ApiScopes::SCOPE_OWNED_CLIMB_UPDATE->value => 'Update a climb you submitted',
        ApiScopes::SCOPE_RANK_METHOD_CREATE->value => 'Create a new rank method',
        ApiScopes::SCOPE_RANK_METHOD_DELETE->value => 'Delete an existing rank method',
        ApiScopes::SCOPE_RANK_METHOD_READ->value => 'View an existing rank method',
        ApiScopes::SCOPE_RANK_METHOD_UPDATE->value => 'Update an existing rank method',
        ApiScopes::SCOPE_SELF_USER_DELETE->value => 'Delete your user account',
        ApiScopes::SCOPE_SELF_USER_UPDATE->value => 'Update your user account',
        ApiScopes::SCOPE_SUBCATEGORY_CREATE->value => 'Create a new subcategory',
        ApiScopes::SCOPE_SUBCATEGORY_DELETE->value => 'Delete an existing subcategory',
        ApiScopes::SCOPE_SUBCATEGORY_READ->value => 'View an existing subcategory',
        ApiScopes::SCOPE_SUBCATEGORY_UPDATE->value => 'Update an existing subcategory',
        ApiScopes::SCOPE_UNOWNED_CLIMB_DELETE->value => 'Delete any submitted climb',
        ApiScopes::SCOPE_UNOWNED_CLIMB_UPDATE->value => 'Update any submitted climb',
        ApiScopes::SCOPE_USER_CREATE->value => 'Create a new user',
        ApiScopes::SCOPE_USER_DELETE->value => 'Delete an existing user',
        ApiScopes::SCOPE_USER_READ->value => 'View an existing user',
        ApiScopes::SCOPE_USER_UPDATE->value => 'Update an existing user',
    ];
}
