<?php

namespace App\Twig;

use App\Repository\ClimbRepository;
use App\Repository\UserRepository;
use App\Util\TimeFormatter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
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
        private readonly RequestStack $requestStack,
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
            new TwigFilter(
                'local_datetime',
                [$this, 'localDateTime']
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
        return TimeFormatter::secondsToTime($seconds);
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

    public function localDateTime(
        ?\DateTimeInterface $date,
        string $format = 'Y-m-d H:i:s',
    ): string {
        if (!$date) {
            return '';
        }

        $timezone = $this->requestStack
            ->getCurrentRequest()
            ?->cookies
            ->get('timezone', 'UTC');

        $localDate = $date->setTimezone(
            new \DateTimeZone($timezone)
        );

        return $localDate->format($format);
    }
}
