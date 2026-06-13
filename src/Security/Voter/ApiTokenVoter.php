<?php

namespace App\Security\Voter;

use App\Entity\ApiToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ApiTokenVoter extends Voter
{
    public const string LIST = 'api_token_list';
    public const string CREATE = 'api_token_create';
    public const string READ = 'api_token_read';
    public const string UPDATE = 'api_token_update';
    public const string DELETE = 'api_token_delete';

    public function __construct(
    ) {
    }

    protected function supports(
        string $attribute,
        mixed $subject,
    ): bool {
        return match ($attribute) {
            self::READ, self::UPDATE, self::DELETE => $subject instanceof ApiToken,
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

        $apiToken = $subject;

        return match ($attribute) {
            self::LIST => $this->canList($token, $vote),
            self::CREATE => $this->canCreate($token, $vote),
            self::READ => $this->canRead($apiToken, $token, $vote),
            self::UPDATE => $this->canUpdate($apiToken, $token, $vote),
            self::DELETE => $this->canDelete($apiToken, $token, $vote),
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
        return true;
    }

    private function canRead(
        ApiToken $apiToken,
        TokenInterface $token,
        ?Vote $vote,
    ): bool {
        if ($token->getUser() === $apiToken->getOwnedBy()) {
            return true;
        }
        $vote?->addReason(sprintf(
            'You (username: %s) do not own this API token.',
            $token->getUser()->getUsername()
        ));

        return false;
    }

    private function canUpdate(
        ApiToken $apiToken,
        TokenInterface $token,
        ?Vote $vote,
    ): bool {
        if ($token->getUser() === $apiToken->getOwnedBy()) {
            return true;
        }
        $vote?->addReason(sprintf(
            'You (username: %s) do not own this API token.',
            $token->getUser()->getUsername()
        ));

        return false;
    }

    private function canDelete(
        mixed $apiToken,
        TokenInterface $token,
        ?Vote $vote
    ): bool {
        if ($token->getUser() === $apiToken->getOwnedBy()) {
            return true;
        }
        $vote?->addReason(sprintf(
            'You (username: %s) do not own this API token.',
            $token->getUser()->getUsername()
        ));

        return false;
    }
}
