<?php

namespace App\Controller;

use App\Repository\ApiTokenRepository;
use App\Repository\ClimbRepository;
use App\Repository\UserRepository;
use App\Security\UserVoter;
use App\Service\BreadcrumbsService;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/climber', name: 'user_')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserRepository $userRepository, BreadcrumbsService $breadcrumbs, Request $request, PaginationService $paginationService): Response
    {
        $climbers = $userRepository->findAllOrderByDisplayName();
        $climbers->setMaxPerPage($request->query->get('perPage', 50));
        $climbers->setCurrentPage($request->query->get('page', 1));

        $pagination = $paginationService->build(
            currentPage: $request->query->get('page', 1),
            totalPages: $climbers->getNbPages(),
            totalResults: $climbers->getNbResults(),
            maxPerPage: $request->query->get('perPage', 50)
        );

        $breadcrumbs
            ->addHome()
            ->addClimber()
        ;

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'climbers' => $climbers,
            'breadcrumbs' => $breadcrumbs->all(),
            'pagination' => $pagination,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(BreadcrumbsService $breadcrumbs): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::CREATE);

        // TODO: Setup user registration form if/when non-discord allowed

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

        // TODO: Setup user profile page, include best ranked run from each category?

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
        $this->denyAccessUnlessGranted(UserVoter::UPDATE, $climber);

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
    public function delete(int $userId, UserRepository $userRepository, BreadcrumbsService $breadcrumbs): Response
    {
        $climber = $userRepository->findOneBy(['id' => $userId]);
        $this->denyAccessUnlessGranted(UserVoter::DELETE, $climber);

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

    #[Route('/api-tokens', name: 'api_tokens')]
    public function apiTokens(ApiTokenRepository $apiTokenRepository, BreadcrumbsService $breadcrumbs, Request $request, PaginationService $paginationService, UserRepository $userRepository): Response
    {
        $apiTokens = $apiTokenRepository->findByOwnerOrderByIndex($this->getUser());
        $apiTokens->setMaxPerPage($request->query->get('perPage', 50));
        $apiTokens->setCurrentPage($request->query->get('page', 1));

        $pagination = $paginationService->build(
            currentPage: $request->query->get('page', 1),
            totalPages: $apiTokens->getNbPages(),
            totalResults: $apiTokens->getNbResults(),
            maxPerPage: $request->query->get('perPage', 50)
        );

        $breadcrumbs
            ->addHome()
            ->addClimber()
            // TODO: Finish API Token Breadcrumb
        ;

        return $this->render('user/api-tokens/index.html.twig', [
            'controller_name' => 'UserController',
            'apiTokens' => $apiTokens,
            'breadcrumbs' => $breadcrumbs->all(),
            'pagination' => $pagination,
        ]);
    }
}
