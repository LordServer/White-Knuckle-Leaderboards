<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/approvals', name: 'approvals_')]
final class ApprovalsController extends AbstractController
{
    #[Route('/{categoryId<\d+>?1}/{subcategoryId<\d+>?1}', name: 'index')]
    public function index(int $categoryId, int $subcategoryId): Response
    {
        return $this->render('approvals/index.html.twig', [
            'controller_name' => 'ApprovalsController',
        ]);
    }
}
