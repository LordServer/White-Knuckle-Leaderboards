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
            new TwigFunction(
                'recent_climbs',
                [$this, 'getRecentClimbs']
            ),
            new TwigFunction(
                'climb_stats',
                [$this, 'getClimbStats']
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

    public function getRecentClimbs(): array
    {
        return $this->climbRepository->getRecentClimbs();
    }

    public function getClimbStats(): array
    {
        return $this->climbRepository->getClimbStats();
    }
}
