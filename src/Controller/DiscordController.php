<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/auth/discord', name:'auth_discord_')]
final class DiscordController
{
    #[Route('/login', name: 'login')]
    public function login(Request $request, ClientRegistry $clientRegistry)
    {
    }
}
