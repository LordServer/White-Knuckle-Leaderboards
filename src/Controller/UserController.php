<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/climber', name: 'user_')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(): Response
    {
        return $this->render('user/create.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/read/{userId<\d+>}', name: 'read')]
    public function read(): Response
    {
        return $this->render('user/read.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/read/{userId<\d+>}/climbs/{categoryId<\d+>?1}/{subcategoryId<\d+>?1}', name: 'climbs')]
    public function climbs(int $userId, int $categoryId, int $subcategoryId): Response
    {
        return $this->render('user/climbs.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/update/{userId<\d+>}', name: 'update')]
    public function update(): Response
    {
        return $this->render('user/update.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/delete/{userId<\d+>}', name: 'delete')]
    public function delete(): Response
    {
        return $this->render('user/delete.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
