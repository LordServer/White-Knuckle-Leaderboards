<?php

namespace App\Security;

use App\Entity\User;
use App\Enum\UserStatus;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        switch ($user->getStatus()) {
            case UserStatus::ACTIVE:
                break;
            case UserStatus::SUSPENDED:
                if (!$user->isBanned()) {
                    break;
                }
                throw new CustomUserMessageAccountStatusException('You have been suspended until '.$user->getBannedUntil()?->format('Y-m-d H:i:s').'.');
            case UserStatus::BANNED:
                throw new CustomUserMessageAccountStatusException('You have been permanently banned.');
        }
    }

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
    }
}
