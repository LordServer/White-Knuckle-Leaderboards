<?php

namespace App\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Wohali\OAuth2\Client\Provider\Discord;

class ExtendedDiscordProvider extends Discord
{
    /**
     * Get provider URL to retrieve user details
     *
     * @param  AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->apiDomain.'/users/@me/guilds/'.$_ENV['DISCORD_GUILD_ID'].'/member';
    }
    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token): ResourceOwnerInterface
    {
        return new ExtendedDiscordResourceOwner($response);
    }
}
