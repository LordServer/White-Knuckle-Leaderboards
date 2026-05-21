<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ClimbRepository;
use App\Repository\SubcategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LeaderboardController extends AbstractController
{
    #[Route('/leaderboard/{categoryId<\d+>?1}/{subcategoryId<\d+>?1}', name: 'app_leaderboard')]
    public function index(int $categoryId, int $subcategoryId, CategoryRepository $categoryRepository, SubcategoryRepository $subcategoryRepository, ClimbRepository $climbRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $category = $categoryRepository->findOneBy(['id' => $categoryId]);
        if (!$category) {
            $category = $categoryRepository->findOneBy(['id' => min(array_map(fn ($item) => $item->getId(), $categories))]);
        }
        $subcategory = $subcategoryRepository->findOneBy(['id' => $subcategoryId]);
        if (!$category->getSubcategory()->contains($subcategory)) {
            $subcategory = $subcategoryRepository->findOneBy(['id' => min(array_map(fn ($item) => $item->getId(), $category->getSubcategory()->toArray()))]);
        }

        $climbs = $climbRepository->findByCategoryAndSubcategoryAndRankSortByRank($category, $subcategory);

        return $this->render('leaderboard/index.html.twig', [
            'controller_name' => 'LeaderboardController',
            'categories' => $categories,
            'category' => $category,
            'subcategory' => $subcategory,
            'climbs' => $climbs,
        ]);
    }
}
