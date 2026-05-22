<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/climber', name: 'user_')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserRepository $userRepository, Breadcrumbs $breadcrumbs): Response
    {
        $users = $userRepository->findAll();

        $breadcrumbs
            ->addHome()
            ->addClimber()
        ;

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs
            ->addHome()
            ->addClimber()
            ->add('Create', 'user_create')
        ;

        return $this->render('user/create.html.twig', [
            'controller_name' => 'UserController',
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/read/{userId<\d+>}', name: 'read')]
    public function read(int $userId, UserRepository $userRepository, Breadcrumbs $breadcrumbs): Response
    {
        $climber = $userRepository->findOneBy(['id' => $userId]);

        if (!$climber) {
            throw $this->createNotFoundException();
        }

        $breadcrumbs
            ->addHome()
            ->addClimber()
            ->add($climber->getDisplayName(), 'user_read', ['userId' => $userId])
        ;

        return $this->render('user/read.html.twig', [
            'controller_name' => 'UserController',
            'climber' => $climber,
            'breadcrumbs' => $breadcrumbs->all(),
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
    public function update(int $userId, UserRepository $userRepository, Breadcrumbs $breadcrumbs): Response
    {
        $climber = $userRepository->findOneBy(['id' => $userId]);

        if (!$climber) {
            throw $this->createNotFoundException();
        }

        $breadcrumbs
            ->addHome()
            ->addClimber()
            ->add($climber->getDisplayName(), 'user_read', ['userId' => $userId])
            ->add('Update', 'user_update', ['userId' => $userId])
        ;

        return $this->render('user/update.html.twig', [
            'controller_name' => 'UserController',
            'climber' => $climber,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/delete/{userId<\d+>}', name: 'delete')]
    public function delete(int $userId, UserRepository $userRepository, Breadcrumbs $breadcrumbs): Response
    {
        $climber = $userRepository->findOneBy(['id' => $userId]);

        if (!$climber) {
            throw $this->createNotFoundException();
        }

        $breadcrumbs
            ->addHome()
            ->addClimber()
            ->add($climber->getDisplayName(), 'user_read', ['userId' => $userId])
            ->add('Delete', 'user_delete', ['userId' => $userId])
        ;

        return $this->render('user/delete.html.twig', [
            'controller_name' => 'UserController',
            'climber' => $climber,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }
}
