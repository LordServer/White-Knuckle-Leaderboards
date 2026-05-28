<?php

namespace App\Security;

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

        return match ($attribute) {
            self::CREATE => $this->canCreate($token, $vote),
            self::READ => $this->canRead(),
            self::UPDATE => $this->canUpdate($token, $vote),
            self::AUTHORIZE => $this->canAuthorize($token, $vote),
            self::DELETE => $this->canDelete($token, $vote),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function canCreate(): bool
    {
        // TODO: Implement canCreate() method.
    }

    private function canRead(): bool
    {
        return true;
    }

    private function canUpdate(): bool
    {
        // TODO: Implement canUpdate() method.
    }

    private function canAuthorize(): bool
    {
        // TODO: Implement canAuthorize() method.
    }

    private function canDelete(): bool
    {
        // TODO: Implement canDelete() method.
    }
}
