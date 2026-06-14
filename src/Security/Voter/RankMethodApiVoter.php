<?php

namespace App\Security\Voter;

use App\ApiResource\RankMethodApi;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class RankMethodApiVoter extends Voter
{
    public const string LIST = 'rank_method_api_list';
    public const string CREATE = 'rank_method_api_create';
    public const string READ = 'rank_method_api_read';
    public const string UPDATE = 'rank_method_api_update';
    public const string DELETE = 'rank_method_api_delete';

    protected function supports(
        string $attribute,
        mixed $subject,
    ): bool {
        return match ($attribute) {
            self::READ, self::UPDATE, self::DELETE => $subject instanceof RankMethodApi,
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

        $rankMethod = $subject;

        return match ($attribute) {
            self::LIST => $this->canList($token, $vote),
            self::CREATE => $this->canCreate($token, $vote),
            self::READ => $this->canRead($rankMethod, $token, $vote),
            self::UPDATE => $this->canUpdate($rankMethod, $token, $vote),
            self::DELETE => $this->canDelete($rankMethod, $token, $vote),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function canList(
        TokenInterface $token,
        ?Vote $vote,
    ): bool {
        if (in_array('ROLE_RANK_METHOD_READ', $token->getRoleNames(), true)) {
            return true;
        }

        $vote?->addReason('This token is missing \'SCOPE_RANK_METHOD_READ\'');
        return false;
    }

    private function canCreate(
        TokenInterface $token,
        ?Vote $vote,
    ): bool {
        if (in_array('ROLE_RANK_METHOD_CREATE', $token->getRoleNames(), true)) {
            return true;
        }

        $vote?->addReason('This token is missing \'SCOPE_RANK_METHOD_CREATE\'');
        return false;
    }

    private function canRead(
        RankMethodApi $rankMethod,
        TokenInterface $token,
        ?Vote $vote,
    ): bool {
        if (in_array('ROLE_RANK_METHOD_READ', $token->getRoleNames(), true)) {
            return true;
        }

        $vote?->addReason('This token is missing \'SCOPE_RANK_METHOD_READ\'');
        return false;
    }

    private function canUpdate(
        RankMethodApi $rankMethod,
        TokenInterface $token,
        ?Vote $vote,
    ): bool {
        if (in_array('ROLE_RANK_METHOD_UPDATE', $token->getRoleNames(), true)) {
            return true;
        }

        $vote?->addReason('This token is missing \'SCOPE_RANK_METHOD_UPDATE\'');
        return false;
    }

    private function canDelete(
        RankMethodApi $rankMethod,
        TokenInterface $token,
        ?Vote $vote,
    ): bool {
        if (in_array('ROLE_RANK_METHOD_DELETE', $token->getRoleNames(), true)) {
            return true;
        }

        $vote?->addReason('This token is missing \'SCOPE_RANK_METHOD_DELETE\'');
        return false;
    }
}
