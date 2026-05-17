<?php

namespace App\Twig;

use App\Repository\ClimbRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Extensions extends AbstractExtension
{
    public function __construct(
        private readonly ClimbRepository $climbRepository,
        private readonly Security $security,
        private readonly UserRepository $userRepository,
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
            new TwigFunction(
                'total_users',
                [$this, 'getTotalUsers']
            ),
            new TwigFunction(
                'active_users',
                [$this, 'getActiveUsers']
            ),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'seconds_to_time',
                [$this, 'secondsToTime']
            ),
            new TwigFilter(
                'ordinalize',
                [$this, 'getOrdinalSuffix']
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

    public function getTotalUsers(): int
    {
        return $this->userRepository->getTotalUsers();
    }

    public function getActiveUsers(): int
    {
        return $this->climbRepository->getRecentUserClimbs();
    }

    public function secondsToTime(float $seconds): string
    {
        $msec = ($seconds * 100) % 100;
        $secs = $seconds % 60;
        $mins = floor($seconds / 60) % 60;
        $hours = floor($seconds / 3600);

        if (0 != $hours) {
            return sprintf('%02d:%02d:%02d.%02d', $hours, $mins, $secs, $msec);
        } elseif (0 != $mins) {
            return sprintf('%02d:%02d.%02d', $mins, $secs, $msec);
        }

        return sprintf('%02d.%02d', $secs, $msec);
    }

    public function getOrdinalSuffix(int $number): string
    {
        $tens = (($number / 10) % 10);
        $ones = substr($number, -1);

        return match ($tens) {
            1 => $number.'th',
            default => match ($ones) {
                '1' => $number.'st',
                '2' => $number.'nd',
                '3' => $number.'rd',
                default => $number.'th',
            },
        };
    }
}
