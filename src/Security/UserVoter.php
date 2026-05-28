<?php

namespace App\Security;

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
        // TODO: Implement supports() method.
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        // TODO: Implement voteOnAttribute() method.
    }

    private function canCreate(): bool
    {
        // TODO: Implement canCreate() method.
    }

    private function canRead(): bool
    {
        // TODO: Implement canRead() method.
    }

    private function canUpdate(): bool
    {
        // TODO: Implement canUpdate() method.
    }

    private function canModerate(): bool
    {
        // TODO: Implement canModerate() method.
    }

    private function canDelete(): bool
    {
        // TODO: Implement canDelete() method.
    }
}
