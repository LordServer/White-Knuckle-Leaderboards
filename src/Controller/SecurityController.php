<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class SecurityController
{
    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        throw new AuthenticationException('Logging out');
    }
}
