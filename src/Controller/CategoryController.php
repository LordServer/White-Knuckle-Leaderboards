<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category', name: 'category_')]
final class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'user' => $user,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_ADMIN');
        $user = $this->getUser();

        return $this->render('category/create.html.twig', [
            'controller_name' => 'CategoryController',
            'user' => $user,
        ]);
    }

    #[Route('/read', name: 'read')]
    public function read(): Response
    {
        $user = $this->getUser();

        return $this->render('category/read.html.twig', [
            'controller_name' => 'CategoryController',
            'user' => $user,
        ]);
    }

    #[Route('/update', name: 'update')]
    public function update(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_ADMIN');
        $user = $this->getUser();

        return $this->render('category/update.html.twig', [
            'controller_name' => 'CategoryController',
            'user' => $user,
        ]);
    }

    #[Route('/delete', name: 'delete')]
    public function delete(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_ADMIN');
        $user = $this->getUser();

        return $this->render('category/delete.html.twig', [
            'controller_name' => 'CategoryController',
            'user' => $user,
        ]);
    }
}
