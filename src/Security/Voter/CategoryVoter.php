<?php

namespace App\Security\Voter;

use App\Entity\Category;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryVoter extends Voter
{
    public const string LIST = 'category_list';
    public const string CREATE = 'category_create';
    public const string READ = 'category_read';
    public const string UPDATE = 'category_update';
    public const string DELETE = 'category_delete';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function supports(
        string $attribute,
        mixed $subject,
    ): bool {
        return match ($attribute) {
            self::READ, self::UPDATE, self::DELETE => $subject instanceof Category,
            self::CREATE, self::LIST => true,
            default => false,
        };
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            $vote?->addReason('You must be logged in to access this resource.');

            return false;
        }

        $category = $subject;

        return match ($attribute) {
            self::LIST => $this->canList($token, $vote),
            self::CREATE => $this->canCreate($token, $vote),
            self::READ => $this->canRead($category, $token, $vote),
            self::UPDATE => $this->canUpdate($category, $token, $vote),
            self::DELETE => $this->canDelete($category, $token, $vote),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function canList(
        TokenInterface $token,
        Vote $vote,
    ): bool {
        return true;
    }

    private function canCreate(
        TokenInterface $token,
        Vote $vote,
    ): bool {
        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        $vote?->addReason(sprintf(
            'The logged in user (username: %s) is not an admin.',
            $token->getUser()->getUsername()
        ));

        return false;
    }

    private function canRead(
        Category $category,
        TokenInterface $token,
        Vote $vote,
    ): bool {
        return true;
    }

    private function canUpdate(
        Category $category,
        TokenInterface $token,
        Vote $vote,
    ): bool {
        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        $vote?->addReason(sprintf(
            'The logged in user (username: %s) is not an admin.',
            $token->getUser()->getUsername()
        ));

        return false;
    }

    private function canDelete(
        Category $category,
        TokenInterface $token,
        Vote $vote,
    ): bool {
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
