<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const string CREATE = 'create';
    public const string READ = 'read';
    public const string UPDATE = 'update';
    public const string MODERATE = 'moderate';
    public const string DELETE = 'delete';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::CREATE, self::READ, self::UPDATE, self::MODERATE, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof User) {
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

        $climber = $subject;

        return match ($attribute) {
            self::CREATE => $this->canCreate($token, $vote),
            self::READ => $this->canRead(),
            self::UPDATE => $this->canUpdate($climber, $token, $vote),
            self::MODERATE => $this->canModerate($token, $vote),
            self::DELETE => $this->canDelete($climber, $token, $vote),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function canCreate(): bool
    {
        // TODO: Implement canCreate() method.
        return false;
    }

    private function canRead(): bool
    {
        return true;
    }

    private function canUpdate(User $climber, TokenInterface $token, ?Vote $vote): bool
    {
        if ($token->getUser() === $climber) {
            return true;
        }
        $vote?->addReason('You may not edit other users');

        if ($this->accessDecisionManager->decide($token, ['ROLE_AUTHORIZER'])) {
            return true;
        }
        $vote?->addReason('You are not an authorizer');

        return false;
    }

    private function canModerate(TokenInterface $token, ?Vote $vote): bool
    {
        if ($this->accessDecisionManager->decide($token, ['ROLE_AUTHORIZER'])) {
            return true;
        }
        $vote?->addReason('You are not an authorizer');

        return false;
    }

    private function canDelete(User $climber, TokenInterface $token, ?Vote $vote): bool
    {
        if ($token->getUser() === $climber) {
            return true;
        }
        $vote?->addReason('You may not delete other users');

        return false;
    }
}
