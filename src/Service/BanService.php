<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\UserStatus;
use Doctrine\ORM\EntityManagerInterface;

class BanService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function refreshBanStatus(User $user): bool
    {
        if ($user->isBanned()) {
            return false;
        }

        if ($user->getBannedUntil() > new \DateTimeImmutable()) {
            return false;
        }

        $user->setStatus(UserStatus::ACTIVE);
        $user->setBannedUntil(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }
}
