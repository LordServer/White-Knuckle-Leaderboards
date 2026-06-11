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
    public const string CREATE = 'api_token_create';
    public const string READ = 'api_token_read';
    public const string UPDATE = 'api_token_update';
    public const string DELETE = 'api_token_delete';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::CREATE, self::READ, self::UPDATE, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof ApiToken) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

        $apiToken = $subject;

        return match ($attribute) {
            self::CREATE => $this->canCreate(),
            self::READ => $this->canRead($apiToken, $token, $vote),
            self::UPDATE => $this->canUpdate($apiToken, $token, $vote),
            self::DELETE => $this->canDelete($apiToken, $token, $vote),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function canCreate(): bool
    {
        return true;
    }

    private function canRead(ApiToken $apiToken, TokenInterface $token, ?Vote $vote): bool
    {
        if ($token->getUser() === $apiToken->getOwnedBy()) {
            return true;
        }
        $vote?->addReason(sprintf(
            'The logged in user (username: %s) does not own this API token.',
            $token->getUser()->getUsername()
        ));

        return false;
    }

    private function canUpdate(ApiToken $apiToken, TokenInterface $token, ?Vote $vote): bool
    {
        if ($token->getUser() === $apiToken->getOwnedBy()) {
            return true;
        }
        $vote?->addReason(sprintf(
            'The logged in user (username: %s) does not own this API token.',
            $token->getUser()->getUsername()
        ));

        return false;
    }

    private function canDelete(mixed $apiToken, TokenInterface $token, ?Vote $vote): bool
    {
        if ($token->getUser() === $apiToken->getOwnedBy()) {
            return true;
        }
        $vote?->addReason(sprintf(
            'The logged in user (username: %s) does not own this API token.',
            $token->getUser()->getUsername()
        ));

        return false;
    }
}
