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
            case UserStatus::SUSPENDED:
            case UserStatus::ACTIVE:
                break;
            case UserStatus::BANNED:
                if (null !== $user->getBannedUntil()) {
                    throw new CustomUserMessageAccountStatusException('You have been banned until '.$user->getBannedUntil());
                }
                throw new CustomUserMessageAccountStatusException('You have been permanently banned');
        }
    }

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
    }
}
