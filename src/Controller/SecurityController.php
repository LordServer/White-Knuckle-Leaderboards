<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class SecurityController
{
    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        flash()->use('theme.ruby')->success('You have been logged out.');
        throw new AuthenticationException('Logging out');
    }
}
