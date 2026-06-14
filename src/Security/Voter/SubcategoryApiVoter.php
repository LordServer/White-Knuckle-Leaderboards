<?php

namespace App\Security\Voter;

use App\ApiResource\SubcategoryApi;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class SubcategoryApiVoter extends Voter
{
    public const string LIST = 'subcategory_api_list';
    public const string CREATE = 'subcategory_api_create';
    public const string READ = 'subcategory_api_read';
    public const string UPDATE = 'subcategory_api_update';
    public const string DELETE = 'subcategory_api_delete';

    protected function supports(
        string $attribute,
        mixed $subject,
    ): bool {
        return match ($attribute) {
            self::READ, self::UPDATE, self::DELETE => $subject instanceof SubcategoryApi,
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
        ?Vote $vote,
    ): bool {
        if (in_array('ROLE_SUBCATEGORY_READ', $token->getRoleNames(), true)) {
            return true;
        }

        $vote?->addReason('This token is missing \'SCOPE_SUBCATEGORY_READ\'');
        return false;
    }

    private function canCreate(
        TokenInterface $token,
        ?Vote $vote,
    ): bool {
        if (in_array('ROLE_SUBCATEGORY_CREATE', $token->getRoleNames(), true)) {
            return true;
        }

        $vote?->addReason('This token is missing \'SCOPE_SUBCATEGORY_CREATE\'');
        return false;
    }

    private function canRead(
        SubcategoryApi $subcategory,
        TokenInterface $token,
        ?Vote $vote,
    ): bool {
        if (in_array('ROLE_SUBCATEGORY_READ', $token->getRoleNames(), true)) {
            return true;
        }

        $vote?->addReason('This token is missing \'SCOPE_SUBCATEGORY_READ\'');
        return false;
    }

    private function canUpdate(
        SubcategoryApi $subcategory,
        TokenInterface $token,
        ?Vote $vote,
    ): bool {
        if (in_array('ROLE_SUBCATEGORY_UPDATE', $token->getRoleNames(), true)) {
            return true;
        }

        $vote?->addReason('This token is missing \'SCOPE_SUBCATEGORY_UPDATE\'');
        return false;
    }

    private function canDelete(
        SubcategoryApi $subcategory,
        TokenInterface $token,
        ?Vote $vote,
    ): bool {
        if (in_array('ROLE_SUBCATEGORY_DELETE', $token->getRoleNames(), true)) {
            return true;
        }

        $vote?->addReason('This token is missing \'SCOPE_SUBCATEGORY_DELETE\'');
        return false;
    }
}
