<?php

namespace App\Provider;

use League\OAuth2\Client\Tool\ArrayAccessorTrait;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class ExtendedDiscordResourceOwner extends DiscordResourceOwner
{
    use ArrayAccessorTrait;
    /**
     * Get resource owner ID
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->getValueByKey($this->response, 'user.id');
    }

    public function getGlobalName(): string
    {
        if (null !== $this->getValueByKey($this->response, 'nick'))
        {
            return $this->getValueByKey($this->response, 'nick');
        } else
        {
            return $this->getValueByKey($this->response, 'user.global_name');
        }
    }

    public function getDiscordRoles(): array
    {
        return $this->getValueByKey($this->response, 'roles');
    }

    public function getAvatarHash(): string
    {
        if (null !== $this->getValueByKey($this->response, 'avatar')) {
            $avatarPath = 'guilds/' . $_ENV['DISCORD_GUILD_ID'] . '/users/' . $this->getValueByKey($this->response, 'user.id') . '/avatars/' . $this->getValueByKey($this->response, 'avatar');
        } else {
            $avatarPath = 'avatars/'.$this->getValueByKey($this->response, 'user.id').'/'.$this->getValueByKey($this->response, 'user.avatar');
        }
        return $avatarPath;
    }
}
