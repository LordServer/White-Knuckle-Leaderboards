<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ClimbRepository;
use App\Repository\SubcategoryRepository;
use App\Service\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LeaderboardController extends AbstractController
{
    #[Route('/leaderboard/{categoryId<\d+>?1}/{subcategoryId<\d+>?1}', name: 'app_leaderboard')]
    public function leaderboard(int $categoryId, int $subcategoryId, CategoryRepository $categoryRepository, SubcategoryRepository $subcategoryRepository, ClimbRepository $climbRepository, Breadcrumbs $breadcrumbs): Response
    {
        $categories = $categoryRepository->findByArchived(false);
        $category = $categoryRepository->findOneBy(['id' => $categoryId]);
        if (!$category) {
            $category = $categoryRepository->findOneBy(['id' => min(array_map(fn ($item) => $item->getId(), $categories))]);
        }
        $subcategory = $subcategoryRepository->findOneBy(['id' => $subcategoryId]);
        if (!$category->getSubcategory()->contains($subcategory)) {
            $subcategory = $subcategoryRepository->findOneBy(['id' => min(array_map(fn ($item) => $item->getId(), $category->getSubcategory()->toArray()))]);
        }

        $climbs = $climbRepository->findByCategoryAndSubcategoryAndRankSortByRank($category, $subcategory);

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
        ]);
    }

    #[Route('/archive/{categoryId<\d+>?1}/{subcategoryId<\d+>?1}', name: 'app_archive')]
    public function archive(int $categoryId, int $subcategoryId, CategoryRepository $categoryRepository, SubcategoryRepository $subcategoryRepository, ClimbRepository $climbRepository, Breadcrumbs $breadcrumbs): Response
    {
        $categories = $categoryRepository->findByArchived(true);
        $category = $categoryRepository->findOneBy(['id' => $categoryId]);
        if (!$category) {
            $category = $categoryRepository->findOneBy(['id' => min(array_map(fn ($item) => $item->getId(), $categories))]);
        }
        $subcategory = $subcategoryRepository->findOneBy(['id' => $subcategoryId]);
        if (!$category->getSubcategory()->contains($subcategory)) {
            $subcategory = $subcategoryRepository->findOneBy(['id' => min(array_map(fn ($item) => $item->getId(), $category->getSubcategory()->toArray()))]);
        }

        $climbs = $climbRepository->findByCategoryAndSubcategoryAndRankSortByRank($category, $subcategory);

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
        ]);
    }
}
