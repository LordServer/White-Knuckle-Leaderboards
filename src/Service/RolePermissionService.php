<?php

namespace App\Service;

use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class RolePermissionService
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function updateRoles(User $user, array $roles): void
    {
        $allowedRoles = $this->getAssignableRoles();

        foreach ($roles as $role) {
            if (!in_array($role, $allowedRoles, true)) {
                throw new AccessDeniedException(sprintf('You cannot assign role "%s".', $role));
            }
        }
    }

    public function getAssignableRoles(): array
    {
        $roles = [];

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $roles['Moderator'] = UserRole::MODERATOR->value;
        }

        if ($this->security->isGranted('ROLE_MODERATOR')) {
            $roles['Authorizer'] = UserRole::AUTHORIZER->value;
            $roles['User'] = UserRole::USER->value;
        }

        return $roles;
    }

    public function canAssign(UserInterface $user, string $role): bool
    {
        return in_array($role, $this->getAssignableRoles($user), true);
    }
}
