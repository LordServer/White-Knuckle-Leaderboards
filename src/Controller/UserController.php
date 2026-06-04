<?php

namespace App\Controller;

use App\Repository\ClimbRepository;
use App\Repository\UserRepository;
use App\Service\BreadcrumbsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/climber', name: 'user_')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserRepository $userRepository, BreadcrumbsService $breadcrumbs): Response
    {
        $climbers = $userRepository->findAll();

        $breadcrumbs
            ->addHome()
            ->addClimber()
        ;

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'climbers' => $climbers,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(BreadcrumbsService $breadcrumbs): Response
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
    public function read(int $userId, UserRepository $userRepository, BreadcrumbsService $breadcrumbs): Response
    {
        $climber = $userRepository->findOneBy(['id' => $userId]);

        if (!$climber) {
            throw $this->createNotFoundException('Climber not found');
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
    public function climbs(int $userId, int $categoryId, int $subcategoryId, UserRepository $userRepository, ClimbRepository $climbRepository, BreadcrumbsService $breadcrumbs): Response
    {
        $climber = $userRepository->findOneBy(['id' => $userId]);

        if (!$climber) {
            throw $this->createNotFoundException('Climber not found');
        }

        $climbs = $climbRepository->findGroupedByClimber($climber);

        $grouped = [];

        foreach ($climbs as $climb) {
            $category = $climb->getCategory();
            $subcategory = $climb->getSubcategory();

            $grouped[$category->getId()]['category'] = $category;
            $grouped[$category->getId()]['subcategories'][$subcategory->getId()]['subcategory'] = $subcategory;
            $grouped[$category->getId()]['subcategories'][$subcategory->getId()]['climbs'][] = $climb;
        }

        $categories = array_map(
            fn ($data) => $data['category'],
            $grouped
        );

        $categoryData = $grouped[$categoryId] ?? reset($grouped);

        if (!$categoryData) {
            throw $this->createNotFoundException('No categories found');
        }

        $category = $categoryData['category'];

        $subcategoryData = $categoryData['subcategories'][$subcategoryId]
            ?? reset($categoryData['subcategories']);

        if (!$subcategoryData) {
            throw $this->createNotFoundException('No subcategories found');
        }

        $subcategory = $subcategoryData['subcategory'];

        $climbs = $subcategoryData['climbs'];

        if ('Normal' !== $subcategory->getName()) {
            $pageName = $subcategory->getName().' '.$category->getName();
        } else {
            $pageName = $category->getName();
        }

        $breadcrumbs
            ->addHome()
            ->addClimber()
            ->add($climber->getDisplayName(), 'user_read', ['userId' => $userId])
            ->add($pageName, 'user_climbs', ['userId' => $userId, 'categoryId' => $categoryId, 'subcategoryId' => $subcategoryId])
        ;

        return $this->render('user/climbs.html.twig', [
            'controller_name' => 'UserController',
            'climber' => $climber,
            'categories' => $categories,
            'category' => $category,
            'subcategory' => $subcategory,
            'climbs' => $climbs,
            'grouped' => $grouped,
            'breadcrumbs' => $breadcrumbs->all(),
            'pageName' => $pageName,
        ]);
    }

    #[Route('/update/{userId<\d+>}', name: 'update')]
    public function update(int $userId, UserRepository $userRepository, BreadcrumbsService $breadcrumbs): Response
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

    #[Route('/tokens', name: 'tokens')]
    public function tokens(): Response
    {
        return $this->render('tokens.html.twig', []);
    }

    #[Route('/delete/{userId<\d+>}', name: 'delete')]
    public function delete(int $userId, UserRepository $userRepository, BreadcrumbsService $breadcrumbs): Response
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
