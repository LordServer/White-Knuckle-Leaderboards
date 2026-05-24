<?php

namespace App\Security;

use App\Entity\Subcategory;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SubcategoryVoter extends Voter
{
    public const string CREATE = 'create';
    public const string READ = 'read';
    public const string UPDATE = 'update';
    public const string DELETE = 'delete';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::CREATE, self::READ, self::UPDATE, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Subcategory) {
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
            self::DELETE => $this->canDelete($token, $vote),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function canCreate(TokenInterface $token, Vote $vote): bool
    {
        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        $vote?->addReason(sprintf(
            'The logged in user (username: %s) is not an admin.',
            $token->getUser()->getUsername()
        ));

        return false;
    }

    private function canRead(): bool
    {
        return true;
    }

    private function canUpdate(TokenInterface $token, Vote $vote): bool
    {
        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        $vote?->addReason(sprintf(
            'The logged in user (username: %s) is not an admin.',
            $token->getUser()->getUsername()
        ));

        return false;
    }

    private function canDelete(TokenInterface $token, Vote $vote): bool
    {
        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        $vote?->addReason(sprintf(
            'The logged in user (username: %s) is not an admin.',
            $token->getUser()->getUsername()
        ));

        return false;
    }
}
