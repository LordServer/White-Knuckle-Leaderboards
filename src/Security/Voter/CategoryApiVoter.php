<?php

namespace App\Security\Voter;

use App\ApiResource\CategoryApi;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class CategoryApiVoter extends Voter
{
    public const string LIST = 'category_api_list';
    public const string CREATE = 'category_api_create';
    public const string READ = 'category_api_read';
    public const string UPDATE = 'category_api_update';
    public const string DELETE = 'category_api_delete';

    protected function supports(
        string $attribute,
        mixed $subject,
    ): bool {
        return match ($attribute) {
            self::READ, self::UPDATE, self::DELETE => $subject instanceof CategoryApi,
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

        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

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
        ?Vote $vote,
    ): bool {
        if (in_array('ROLE_CATEGORY_READ', $token->getRoleNames(), true)) {
            return true;
        }

        $vote?->addReason('This token is missing \'SCOPE_CATEGORY_READ\'');
        return false;
    }

    private function canCreate(
        TokenInterface $token,
        ?Vote $vote,
    ): bool {
        if (in_array('ROLE_CATEGORY_CREATE', $token->getRoleNames(), true)) {
            return true;
        }

        $vote?->addReason('This token is missing \'SCOPE_CATEGORY_CREATE\'');
        return false;
    }

    private function canRead(
        CategoryApi $category,
        TokenInterface $token,
        ?Vote $vote,
    ): bool {
        if (in_array('ROLE_CATEGORY_READ', $token->getRoleNames(), true)) {
            return true;
        }

        $vote?->addReason('This token is missing \'SCOPE_CATEGORY_READ\'');
        return false;
    }

    private function canUpdate(
        CategoryApi $category,
        TokenInterface $token,
        ?Vote $vote,
    ): bool {
        if (in_array('ROLE_CATEGORY_UPDATE', $token->getRoleNames(), true)) {
            return true;
        }

        $vote?->addReason('This token is missing \'SCOPE_CATEGORY_UPDATE\'');
        return false;
    }

    private function canDelete(
        CategoryApi $category,
        TokenInterface $token,
        ?Vote $vote,
    ): bool {
        if (in_array('ROLE_CATEGORY_DELETE', $token->getRoleNames(), true)) {
            return true;
        }

        $vote?->addReason('This token is missing \'SCOPE_CATEGORY_DELETE\'');
        return false;
    }
}
