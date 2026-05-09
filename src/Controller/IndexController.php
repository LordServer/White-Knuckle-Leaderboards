<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('index.html.twig', [
            'controller_name' => 'IndexController',
            'user' => $user,
        ]);
    }
}
