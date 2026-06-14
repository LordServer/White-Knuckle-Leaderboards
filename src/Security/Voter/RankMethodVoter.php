<?php

namespace App\Security\Voter;

use App\Entity\RankMethod;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RankMethodVoter extends Voter
{
    public const string LIST = 'rank_method_list';
    public const string CREATE = 'rank_method_create';
    public const string READ = 'rank_method_read';
    public const string UPDATE = 'rank_method_update';
    public const string DELETE = 'rank_method_delete';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function supports(
        string $attribute,
        mixed $subject,
    ): bool {
        return match ($attribute) {
            self::READ, self::UPDATE, self::DELETE => $subject instanceof RankMethod,
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
        RankMethod $rankMethod,
        TokenInterface $token,
        Vote $vote,
    ): bool {
        return true;
    }

    private function canUpdate(
        RankMethod $rankMethod,
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
        RankMethod $rankMethod,
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
