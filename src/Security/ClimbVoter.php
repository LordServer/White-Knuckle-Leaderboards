<?php

namespace App\Security;

use App\Entity\Climb;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ClimbVoter extends Voter
{
    public const string CREATE = 'create';
    public const string READ = 'read';
    public const string UPDATE = 'update';
    public const string AUTHORIZE = 'authorize';
    public const string DELETE = 'delete';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::CREATE, self::READ, self::UPDATE, self::AUTHORIZE, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Climb) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            $vote?->addReason('The user is not logged in.');

            return false;
        }

        $climb = $subject;

        return match ($attribute) {
            self::CREATE => $this->canCreate($token, $vote),
            self::READ => $this->canRead(),
            self::UPDATE => $this->canUpdate($climb, $token, $vote),
            self::AUTHORIZE => $this->canAuthorize($subject, $token, $vote),
            self::DELETE => $this->canDelete($climb, $token, $vote),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function canCreate(): bool
    {
        return true;
    }

    private function canRead(): bool
    {
        return true;
    }

    private function canUpdate(Climb $climb, TokenInterface $token, ?Vote $vote): bool
    {
        if ($token->getUser() === $climb->getClimber() && false === $climb->isReviewed()) {
            return true;
        } elseif ($token->getUser() !== $climb->getClimber()) {
            $vote?->addReason(sprintf(
                'The logged in user (username: %s) does not own this climb.',
                $token->getUser()->getUsername()
            ));
        } else {
            $vote?->addReason('The climb has already been approved and can no longer be edited.');
        }

        if ($this->accessDecisionManager->decide($token, ['ROLE_AUTHORIZER']) && false === $climb->isReviewed()) {
            return true;
        } elseif (!$this->accessDecisionManager->decide($token, ['ROLE_AUTHORIZER'])) {
            $vote?->addReason(sprintf(
                'The logged in user (username: %s) is not an authorizer.',
                $token->getUser()->getUsername()
            ));
        } else {
            $vote?->addReason('The climb has already been approved and can no longer be edited.');
        }

        if ($this->accessDecisionManager->decide($token, ['ROLE_MODERATOR'])) {
            return true;
        }
        $vote?->addReason(sprintf(
            'The logged in user (username: %s) is not a moderator.',
            $token->getUser()->getUsername()
        ));

        return false;
    }

    private function canAuthorize(Climb $climb, TokenInterface $token, ?Vote $vote): bool
    {
        if ($token->getUser() !== $climb->getClimber() && $this->accessDecisionManager->decide($token, ['ROLE_AUTHORIZER']) && false === $climb->isReviewed()) {
            return true;
        } elseif ($token->getUser() === $climb->getClimber()) {
            $vote?->addReason('You cannot approve your own submissions.');
        } elseif (!$this->accessDecisionManager->decide($token, ['ROLE_AUTHORIZER'])) {
            $vote?->addReason(sprintf(
                'The logged in user (username: %s) is not an authorizer.',
                $token->getUser()->getUsername()
            ));
        } elseif (true === $climb->isReviewed()) {
            $vote?->addReason('This climb has already been reviewed and can no longer be edited.');
        }

        // TODO: Add moderator ability to edit already approved climbs.

        return false;
    }

    private function canDelete(Climb $climb, TokenInterface $token, ?Vote $vote): bool
    {
        if ($token->getUser() === $climb->getClimber()) {
            return true;
        }
        $vote?->addReason(sprintf(
            'The logged in user (username: %s) does not own this climb.',
            $token->getUser()->getUsername()
        ));

        if ($this->accessDecisionManager->decide($token, ['ROLE_MODERATOR'])) {
            return true;
        }
        $vote?->addReason(sprintf(
            'The logged in user (username: %s) is not a moderator.',
            $token->getUser()->getUsername()
        ));

        return false;
    }
}
