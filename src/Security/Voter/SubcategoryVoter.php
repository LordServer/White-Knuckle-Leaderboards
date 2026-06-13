<?php

namespace App\Security\Voter;

use App\Entity\Subcategory;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SubcategoryVoter extends Voter
{
    public const string LIST = 'subcategory_list';
    public const string CREATE = 'subcategory_create';
    public const string READ = 'subcategory_read';
    public const string UPDATE = 'subcategory_update';
    public const string DELETE = 'subcategory_delete';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function supports(
        string $attribute,
        mixed $subject,
    ): bool {
        return match ($attribute) {
            self::READ, self::UPDATE, self::DELETE => $subject instanceof Subcategory,
            self::CREATE, self::LIST => true,
            default => false,
        };
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null,
    ): bool {
        $user = $token->getUser();

        if (!$user instanceof User) {
            $vote?->addReason('The user is not logged in.');

            return false;
        }

        $subcategory = $subject;

        return match ($attribute) {
            self::LIST => $this->canList($token, $vote),
            self::CREATE => $this->canCreate($token, $vote),
            self::READ => $this->canRead($subcategory, $token, $vote),
            self::UPDATE => $this->canUpdate($subcategory, $token, $vote),
            self::DELETE => $this->canDelete($subcategory, $token, $vote),
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
        Subcategory $subcategory,
        TokenInterface $token,
        Vote $vote,
    ): bool {
        return true;
    }

    private function canUpdate(
        Subcategory $subcategory,
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
        Subcategory $subcategory,
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
