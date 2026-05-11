<?php

namespace App\Controller;

use App\Repository\SubcategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/subcategory', name: 'subcategory_')]
final class SubcategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SubcategoryRepository $subcategoryRepository): Response
    {
        $user = $this->getUser();
        $subcategories = $subcategoryRepository->findAll();

        return $this->render('subcategory/index.html.twig', [
            'controller_name' => 'SubcategoryController',
            'user' => $user,
            'subcategories' => $subcategories,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_ADMIN');
        $user = $this->getUser();

        return $this->render('subcategory/create.html.twig', [
            'controller_name' => 'SubcategoryController',
            'user' => $user,
        ]);
    }

    #[Route('/read', name: 'read')]
    public function read(): Response
    {
        $user = $this->getUser();

        return $this->render('subcategory/read.html.twig', [
            'controller_name' => 'SubcategoryController',
            'user' => $user,
        ]);
    }

    #[Route('/update', name: 'update')]
    public function update(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_ADMIN');
        $user = $this->getUser();

        return $this->render('subcategory/update.html.twig', [
            'controller_name' => 'SubcategoryController',
            'user' => $user,
        ]);
    }

    #[Route('/delete', name: 'delete')]
    public function delete(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_ADMIN');
        $user = $this->getUser();

        return $this->render('subcategory/delete.html.twig', [
            'controller_name' => 'SubcategoryController',
            'user' => $user,
        ]);
    }
}
