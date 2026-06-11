<?php

namespace App\Service;

use App\Enum\ApiScopes;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

readonly class ScopePermissionService
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function assignScopes(array $scopes)
    {
        $allowedScopes = $this->getAssignableScopes();

        foreach ($scopes as $scope) {
            if (!in_array($scope, $allowedScopes, true)) {
                throw new AccessDeniedException(sprintf('You cannot assign scope "%s".', $scope));
            }
        }
    }

    public function getAssignableScopes(): array
    {
        $scopes = [];

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $scopes[ApiScopes::SCOPE_CATEGORY_CREATE->name] = ApiScopes::SCOPE_CATEGORY_CREATE->value;
            $scopes[ApiScopes::SCOPE_CATEGORY_DELETE->name] = ApiScopes::SCOPE_CATEGORY_DELETE->value;
            $scopes[ApiScopes::SCOPE_CATEGORY_UPDATE->name] = ApiScopes::SCOPE_CATEGORY_UPDATE->value;
            $scopes[ApiScopes::SCOPE_RANK_METHOD_CREATE->name] = ApiScopes::SCOPE_RANK_METHOD_CREATE->value;
            $scopes[ApiScopes::SCOPE_RANK_METHOD_DELETE->name] = ApiScopes::SCOPE_RANK_METHOD_DELETE->value;
            $scopes[ApiScopes::SCOPE_RANK_METHOD_UPDATE->name] = ApiScopes::SCOPE_RANK_METHOD_UPDATE->value;
            $scopes[ApiScopes::SCOPE_SUBCATEGORY_CREATE->name] = ApiScopes::SCOPE_SUBCATEGORY_CREATE->value;
            $scopes[ApiScopes::SCOPE_SUBCATEGORY_DELETE->name] = ApiScopes::SCOPE_SUBCATEGORY_DELETE->value;
            $scopes[ApiScopes::SCOPE_SUBCATEGORY_UPDATE->name] = ApiScopes::SCOPE_SUBCATEGORY_UPDATE->value;
        }

        if ($this->security->isGranted('ROLE_MODERATOR')) {
            $scopes[ApiScopes::SCOPE_USER_CREATE->name] = ApiScopes::SCOPE_USER_CREATE->value;
            $scopes[ApiScopes::SCOPE_USER_DELETE->name] = ApiScopes::SCOPE_USER_DELETE->value;
        }

        if ($this->security->isGranted('ROLE_AUTHORIZER')) {
            $scopes[ApiScopes::SCOPE_APPROVALS_READ->name] = ApiScopes::SCOPE_APPROVALS_READ->value;
            $scopes[ApiScopes::SCOPE_CLIMB_REVIEW->name] = ApiScopes::SCOPE_CLIMB_REVIEW->value;
            $scopes[ApiScopes::SCOPE_MODERATE_USER->name] = ApiScopes::SCOPE_MODERATE_USER->value;
            $scopes[ApiScopes::SCOPE_UNOWNED_CLIMB_DELETE->name] = ApiScopes::SCOPE_UNOWNED_CLIMB_DELETE->value;
            $scopes[ApiScopes::SCOPE_UNOWNED_CLIMB_UPDATE->name] = ApiScopes::SCOPE_UNOWNED_CLIMB_UPDATE->value;
            $scopes[ApiScopes::SCOPE_USER_UPDATE->name] = ApiScopes::SCOPE_USER_UPDATE->value;
        }

        $scopes[ApiScopes::SCOPE_CATEGORY_READ->name] = ApiScopes::SCOPE_CATEGORY_READ->value;
        $scopes[ApiScopes::SCOPE_CLIMB_CREATE->name] = ApiScopes::SCOPE_CLIMB_CREATE->value;
        $scopes[ApiScopes::SCOPE_CLIMB_READ->name] = ApiScopes::SCOPE_CLIMB_READ->value;
        $scopes[ApiScopes::SCOPE_OWNED_CLIMB_DELETE->name] = ApiScopes::SCOPE_OWNED_CLIMB_DELETE->value;
        $scopes[ApiScopes::SCOPE_OWNED_CLIMB_UPDATE->name] = ApiScopes::SCOPE_OWNED_CLIMB_UPDATE->value;
        $scopes[ApiScopes::SCOPE_RANK_METHOD_READ->name] = ApiScopes::SCOPE_RANK_METHOD_READ->value;
        $scopes[ApiScopes::SCOPE_SELF_USER_DELETE->name] = ApiScopes::SCOPE_SELF_USER_DELETE->value;
        $scopes[ApiScopes::SCOPE_SELF_USER_UPDATE->name] = ApiScopes::SCOPE_SELF_USER_UPDATE->value;
        $scopes[ApiScopes::SCOPE_SUBCATEGORY_READ->name] = ApiScopes::SCOPE_SUBCATEGORY_READ->value;
        $scopes[ApiScopes::SCOPE_USER_READ->name] = ApiScopes::SCOPE_USER_READ->value;

        return $scopes;
    }
}
