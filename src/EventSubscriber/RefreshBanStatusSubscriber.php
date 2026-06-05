<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\BanService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

class RefreshBanStatusSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly BanService $banService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckPassportEvent::class => 'onCheckPassport',
        ];
    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $user = $event->getPassport()->getUser();

        if (!$user instanceof User) {
            return;
        }

        $this->banService->refreshBanStatus($user);
    }

}
