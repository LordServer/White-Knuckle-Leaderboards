<?php

namespace App\Factory;

use App\Entity\ApiToken;
use App\Enum\ApiScopes;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<ApiToken>
 */
final class ApiTokenFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return ApiToken::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'ownedBy' => UserFactory::random(),
            'scopes' => [
                ApiScopes::SCOPE_CLIMB_CREATE,
                ApiScopes::SCOPE_USER_UPDATE,
            ],
            'isEnabled' => true,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(ApiToken $apiToken): void {})
        ;
    }
}
