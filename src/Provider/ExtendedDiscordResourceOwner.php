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

    public function getGlobalName()
    {
        if (null !== $this->getValueByKey($this->response, 'nick'))
        {
            return $this->getValueByKey($this->response, 'nick');
        } else
        {
            return $this->getValueByKey($this->response, 'user.global_name');
        }
    }

    public function getDiscordRoles()
    {
        return $this->getValueByKey($this->response, 'roles');
    }

    public function getAvatarHash()
    {
        if (null !== $this->getValueByKey($this->response, 'avatar'))
        {
            return $this->getValueByKey($this->response, 'avatar');
        } else
        {
            return $this->getValueByKey($this->response, 'user.avatar');
        }
    }
}
