<?php

namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;

readonly class ScopePermissionService
{
    public function __construct(
        private Security $security,
    ) {
    }
}
