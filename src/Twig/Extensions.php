<?php

namespace App\Twig;

use App\Repository\ClimbRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Extensions extends AbstractExtension
{
    public function __construct(
        private readonly ClimbRepository $climbRepository,
        private readonly Security $security,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'approvals',
                [$this, 'getApprovals']
            ),
            new TwigFunction(
                'current_user',
                [$this, 'getCurrentUser']
            ),
        ];
    }

    public function getApprovals(): int
    {
        return $this->climbRepository->getApprovals();
    }

    public function getCurrentUser(): UserInterface
    {
        return $this->security->getUser();
    }
}
