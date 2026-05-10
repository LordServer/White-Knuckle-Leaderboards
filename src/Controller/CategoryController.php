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
        return $this->render('subcategory/index.html.twig', [
            'controller_name' => 'SubcategoryController',
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(): Response
    {
        return $this->render('subcategory/create.html.twig', [
            'controller_name' => 'SubcategoryController',
        ]);
    }

    #[Route('/read', name: 'read')]
    public function read(): Response
    {
        return $this->render('subcategory/read.html.twig', [
            'controller_name' => 'SubcategoryController',
        ]);
    }

    #[Route('/update', name: 'update')]
    public function update(): Response
    {
        return $this->render('subcategory/update.html.twig', [
            'controller_name' => 'SubcategoryController',
        ]);
    }

    #[Route('/delete', name: 'delete')]
    public function delete(): Response
    {
        return $this->render('subcategory/delete.html.twig', [
            'controller_name' => 'SubcategoryController',
        ]);
    }
}
