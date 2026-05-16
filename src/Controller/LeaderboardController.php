<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\SubcategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LeaderboardController extends AbstractController
{
    #[Route('/leaderboard/{categoryId<\d+>?1}/{subcategoryId<\d+>?1}', name: 'app_leaderboard')]
    public function index(int $categoryId, int $subcategoryId, CategoryRepository $categoryRepository, SubcategoryRepository $subcategoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $category = $categoryRepository->findOneBy(['id' => $categoryId]);
        $subcategory = $subcategoryRepository->findOneBy(['id' => $subcategoryId]);

        return $this->render('leaderboard/index.html.twig', [
            'controller_name' => 'LeaderboardController',
            'categories' => $categories,
            'category' => $category,
            'subcategory' => $subcategory,
        ]);
    }
}
