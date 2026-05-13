<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Climb;
use App\Form\ClimbType;
use App\Repository\CategoryRepository;
use App\Repository\ClimbRepository;
use App\Repository\SubcategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/climb', name: 'climb_')]
final class ClimbController extends AbstractController
{
    #[Route('/{categoryId<\d+>?1}/{subcategoryId<\d+>?1}', name: 'index')]
    public function index(int $categoryId, int $subcategoryId, CategoryRepository $categoryRepository, SubcategoryRepository $subcategoryRepository): Response
    {
        $user = $this->getUser();
        $categories = $categoryRepository->findAll();
        $category = $categoryRepository->findOneBy(['id' => $categoryId]);
        $subcategory = $subcategoryRepository->findOneBy(['id' => $subcategoryId]);

        return $this->render('climb/index.html.twig', [
            'controller_name' => 'ClimbController',
            'user' => $user,
            'categories' => $categories,
            'category' => $category,
            'subcategory' => $subcategory,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $climb = new Climb();

        $form = $this->createForm(ClimbType::class, $climb);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $climb = $form->getData();
            $climb->setClimber($user);
            $climb->setIsReviewed(false);
            $climb->setStatus('unreviewed');
            $climb->setCreatedAt(new \DateTimeImmutable());
            $climb->setUpdatedAt(new \DateTime());

            $entityManager->persist($climb);
            $entityManager->flush();

            return $this->redirectToRoute('climb_read', ['climbId' => $climb->getId()]);
        }

        return $this->render('climb/create.html.twig', [
            'controller_name' => 'ClimbController',
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/read/{climbId<\d+>}', name: 'read')]
    public function read(int $climbId, ClimbRepository $climbRepository): Response
    {
        $user = $this->getUser();
        $climb = $climbRepository->findOneBy(['id' => $climbId]);

        if (!$climb) {
            throw $this->createNotFoundException('Climb not found');
        }

        return $this->render('climb/read.html.twig', [
            'controller_name' => 'ClimbController',
            'user' => $user,
            'climb' => $climb,
        ]);
    }

    #[Route('/update', name: 'update')]
    public function update(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('climb/update.html.twig', [
            'controller_name' => 'ClimbController',
            'user' => $user,
        ]);
    }

    #[Route('/delete', name: 'delete')]
    public function delete(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('climb/delete.html.twig', [
            'controller_name' => 'ClimbController',
            'user' => $user,
        ]);
    }
}
