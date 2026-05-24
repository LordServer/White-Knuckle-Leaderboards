<?php

namespace App\Service;

use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Entity\User;
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
            $roles['Moderator'] = 'ROLE_MODERATOR';
        }

        if ($this->security->isGranted('ROLE_MODERATOR')) {
            $roles['Authorizer'] = 'ROLE_AUTHORIZER';
            $roles['User'] = 'ROLE_USER';
        }

        return $roles;
    }

    public function canAssign(UserInterface $user, string $role): bool
    {
        return in_array($role, $this->getAssignableRoles($user), true);
    }
}
