<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category', name: 'category_')]
final class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $user = $this->getUser();
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'user' => $user,
            'categories' => $categories,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_ADMIN');
        $user = $this->getUser();
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_read', ['categoryId' => $category->getId()]);
        }

        return $this->render('category/create.html.twig', [
            'controller_name' => 'CategoryController',
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/read/{categoryId<\d+>}', name: 'read')]
    public function read(int $categoryId, CategoryRepository $categoryRepository): Response
    {
        $user = $this->getUser();
        $category = $categoryRepository->findOneBy(['id' => $categoryId]);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        return $this->render('category/read.html.twig', [
            'controller_name' => 'CategoryController',
            'user' => $user,
            'category' => $category,
        ]);
    }

    #[Route('/update/{categoryId<\d+>}', name: 'update')]
    public function update(int $categoryId, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_ADMIN');
        $user = $this->getUser();
        $category = $categoryRepository->findOneBy(['id' => $categoryId]);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_read', ['categoryId' => $category->getId()]);
        }

        return $this->render('category/update.html.twig', [
            'controller_name' => 'CategoryController',
            'user' => $user,
            'form' => $form,
            'category' => $category,
        ]);
    }

    #[Route('/delete/{categoryId<\d+>}', name: 'delete')]
    public function delete(int $categoryId, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_ADMIN');
        $user = $this->getUser();
        $category = $categoryRepository->findOneBy(['id' => $categoryId]);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->remove($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/delete.html.twig', [
            'controller_name' => 'CategoryController',
            'user' => $user,
            'form' => $form,
            'category' => $category,
        ]);
    }
}
