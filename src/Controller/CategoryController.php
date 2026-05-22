<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\Breadcrumbs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category', name: 'category_')]
final class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository, Breadcrumbs $breadcrumbs): Response
    {
        $categories = $categoryRepository->findAll();

        $breadcrumbs
            ->addHome()
            ->addCategory()
        ;

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager, Breadcrumbs $breadcrumbs): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_ADMIN');
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_read', ['categoryId' => $category->getId()]);
        }

        $breadcrumbs
            ->addHome()
            ->addCategory()
            ->add('Create', 'category_create')
        ;

        return $this->render('category/create.html.twig', [
            'controller_name' => 'CategoryController',
            'form' => $form,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/read/{categoryId<\d+>}', name: 'read')]
    public function read(int $categoryId, CategoryRepository $categoryRepository, Breadcrumbs $breadcrumbs): Response
    {
        $category = $categoryRepository->findOneBy(['id' => $categoryId]);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $breadcrumbs
            ->addHome()
            ->addCategory()
            ->add($category->getName(), 'category_read', ['categoryId' => $categoryId])
        ;

        return $this->render('category/read.html.twig', [
            'controller_name' => 'CategoryController',
            'category' => $category,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/update/{categoryId<\d+>}', name: 'update')]
    public function update(int $categoryId, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManager, Breadcrumbs $breadcrumbs): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_ADMIN');
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

        $breadcrumbs
            ->addHome()
            ->addCategory()
            ->add($category->getName(), 'category_read', ['categoryId' => $categoryId])
            ->add('Update', 'category_update', ['categoryId' => $categoryId])
        ;

        return $this->render('category/update.html.twig', [
            'controller_name' => 'CategoryController',
            'form' => $form,
            'category' => $category,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/delete/{categoryId<\d+>}', name: 'delete')]
    public function delete(int $categoryId, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManager, Breadcrumbs $breadcrumbs): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_ADMIN');
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

        $breadcrumbs
            ->addHome()
            ->addCategory()
            ->add($category->getName(), 'category_read', ['categoryId' => $categoryId])
            ->add('Delete', 'category_delete', ['categoryId' => $categoryId])
        ;

        return $this->render('category/delete.html.twig', [
            'controller_name' => 'CategoryController',
            'form' => $form,
            'category' => $category,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }
}
