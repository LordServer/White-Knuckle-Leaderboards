<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/climb', name: 'climb_')]
final class ClimbController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('climb/index.html.twig', [
            'controller_name' => 'ClimbController',
            'user' => $user,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('climb/create.html.twig', [
            'controller_name' => 'ClimbController',
            'user' => $user,
        ]);
    }

    #[Route('/read', name: 'read')]
    public function read(): Response
    {
        $user = $this->getUser();

        return $this->render('climb/read.html.twig', [
            'controller_name' => 'ClimbController',
            'user' => $user,
        ]);
    }

    #[Route('/update', name: 'update')]
    public function update(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('climb/update.html.twig', [
            'controller_name' => 'ClimbController',
            'user' => $user,
        ]);
    }

    #[Route('/delete', name: 'delete')]
    public function delete(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('climb/delete.html.twig', [
            'controller_name' => 'ClimbController',
            'user' => $user,
        ]);
    }
}
