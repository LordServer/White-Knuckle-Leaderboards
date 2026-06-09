<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ClimbRepository;
use App\Repository\SubcategoryRepository;
use App\Service\BreadcrumbsService;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LeaderboardController extends AbstractController
{
    #[Route('/leaderboard/{categoryId<\d+>?1}/{subcategoryId<\d+>?1}', name: 'app_leaderboard')]
    public function leaderboard(int $categoryId, int $subcategoryId, CategoryRepository $categoryRepository, ClimbRepository $climbRepository, Request $request, PaginationService $paginationService, BreadcrumbsService $breadcrumbs): Response
    {
        $categories = $categoryRepository->findByArchived(false);

        $category = current(array_filter(
            $categories,
            fn ($category) => $category->getId() === $categoryId
        ));
        $category = $category ?: ($categories[0] ?? null);

        $subcategory = current(array_filter(
            $category->getSubcategory()->toArray(),
            fn ($subcategory) => $subcategory->getId() === $subcategoryId
        ));
        $subcategory = $subcategory ?: ($category->getSubcategory()->toArray()[0] ?? null);

        $climbs = $climbRepository->findByCategoryAndSubcategoryAndRankSortByRank($category, $subcategory);
        $climbs->setMaxPerPage($request->query->get('perPage', 50));
        $climbs->setCurrentPage($request->query->get('page', 1));

        $pagination = $paginationService->build(
            currentPage: $request->query->get('page', 1),
            totalPages: $climbs->getNbPages(),
            totalResults: $climbs->getNbResults(),
            maxPerPage: $request->query->get('perPage', 50)
        );

        if ('Normal' === $subcategory->getName()) {
            $pageName = $category->getName();
        } else {
            $pageName = $subcategory->getName().' '.$category->getName();
        }

        $breadcrumbs
            ->addHome()
            ->addLeaderboard()
            ->add($pageName, 'app_leaderboard', ['categoryId' => $category->getId(), 'subcategoryId' => $subcategory->getId()])
        ;

        return $this->render('leaderboard/leaderboard.html.twig', [
            'controller_name' => 'LeaderboardController',
            'categories' => $categories,
            'category' => $category,
            'subcategory' => $subcategory,
            'climbs' => $climbs,
            'breadcrumbs' => $breadcrumbs->all(),
            'pageName' => $pageName,
            'pagination' => $pagination,
        ]);
    }

    #[Route('/archive/{categoryId<\d+>?1}/{subcategoryId<\d+>?1}', name: 'app_archive')]
    public function archive(int $categoryId, int $subcategoryId, CategoryRepository $categoryRepository, ClimbRepository $climbRepository, BreadcrumbsService $breadcrumbs, Request $request, PaginationService $paginationService): Response
    {
        $categories = $categoryRepository->findByArchived(true);

        $category = current(array_filter(
            $categories,
            fn ($category) => $category->getId() === $categoryId
        ));
        $category = $category ?: ($categories[0] ?? null);

        $subcategory = current(array_filter(
            $category->getSubcategory()->toArray(),
            fn ($subcategory) => $subcategory->getId() === $subcategoryId
        ));
        $subcategory = $subcategory ?: ($category->getSubcategory()->toArray()[0] ?? null);

        $climbs = $climbRepository->findByCategoryAndSubcategoryAndRankSortByRank($category, $subcategory);
        $climbs->setMaxPerPage($request->query->get('perPage', 50));
        $climbs->setCurrentPage($request->query->get('page', 1));

        $pagination = $paginationService->build(
            currentPage: $request->query->get('page', 1),
            totalPages: $climbs->getNbPages(),
            totalResults: $climbs->getNbResults(),
            maxPerPage: $request->query->get('perPage', 50)
        );

        if ('Normal' === $subcategory->getName()) {
            $pageName = $category->getName();
        } else {
            $pageName = $subcategory->getName().' '.$category->getName();
        }

        $breadcrumbs
            ->addHome()
            ->add('Archive', 'app_archive')
            ->add($pageName, 'app_archive', ['categoryId' => $category->getId(), 'subcategoryId' => $subcategory->getId()])
        ;

        return $this->render('leaderboard/archive.html.twig', [
            'controller_name' => 'LeaderboardController',
            'categories' => $categories,
            'category' => $category,
            'subcategory' => $subcategory,
            'climbs' => $climbs,
            'breadcrumbs' => $breadcrumbs->all(),
            'pageName' => $pageName,
            'pagination' => $pagination,
        ]);
    }
}
