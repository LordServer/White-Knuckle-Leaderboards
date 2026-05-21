<?php

namespace App\Controller;

use App\Entity\Climb;
use App\Enum\Status;
use App\Form\ClimbType;
use App\Repository\CategoryRepository;
use App\Repository\ClimbRepository;
use App\Repository\SubcategoryRepository;
use App\Service\UpdateClimbRanks;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/climb', name: 'climb_')]
final class ClimbController extends AbstractController
{
    #[Route('/{categoryId<\d+>?1}/{subcategoryId<\d+>?1}', name: 'index')]
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
        $climbs = $climbRepository->findByCategoryAndSubcategorySortByCreateAt($category, $subcategory);

        return $this->render('climb/index.html.twig', [
            'controller_name' => 'ClimbController',
            'categories' => $categories,
            'category' => $category,
            'subcategory' => $subcategory,
            'climbs' => $climbs,
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
            $climb->setRank(null);

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
        $climb = $climbRepository->findOneBy(['id' => $climbId]);

        if (!$climb) {
            throw $this->createNotFoundException('Climb not found');
        }

        return $this->render('climb/read.html.twig', [
            'controller_name' => 'ClimbController',
            'climb' => $climb,
        ]);
    }

    #[Route('/update/{climbId<\d+>}', name: 'update')]
    public function update(int $climbId, ClimbRepository $climbRepository, Request $request, EntityManagerInterface $entityManager, UpdateClimbRanks $updateClimbRanks): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $climb = $climbRepository->findOneBy(['id' => $climbId]);
        $approved = false;

        if (!$climb) {
            throw $this->createNotFoundException('Climb not found');
        }

        if ($climb->isReviewed() && !$this->isGranted('ROLE_DISCORD_ADMIN')) {
            throw $this->createAccessDeniedException('Climb has already been reviewed and can no longer be updated');
        }

        if (($climb->getClimber()->getId() !== $user->getId()) && !$this->isGranted('ROLE_DISCORD_AUTHORIZER')) {
            throw $this->createAccessDeniedException('You do not have permission to edit this climb');
        }

        $form = $this->createForm(ClimbType::class, $climb);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $climb = $form->getData();

            if ($form->get('approve')->isClicked()) {
                if ($climb->getClimber()->getId() === $user->getId()) {
                    throw $this->createAccessDeniedException('You can not approve your own submissions');
                }
                $climb->setStatus(Status::APPROVED);
                $climb->setVerifier($user);
                $climb->setIsReviewed(true);
                $approved = true;
            } elseif ($form->get('reject')->isClicked()) {
                $climb->setStatus(Status::REJECTED);
                $climb->setVerifier($user);
                $climb->setIsReviewed(true);
            }

            $entityManager->persist($climb);
            $entityManager->flush();

            if ($approved) {
                $updateClimbRanks->updateClimbRanks($climb->getCategory(), $climb->getSubcategory());
            }

            return $this->redirectToRoute('climb_read', ['climbId' => $climb->getId()]);
        }

        return $this->render('climb/update.html.twig', [
            'controller_name' => 'ClimbController',
            'user' => $user,
            'form' => $form,
            'climb' => $climb,
        ]);
    }

    #[Route('/delete/{climbId<\d+>}', name: 'delete')]
    public function delete(int $climbId, ClimbRepository $climbRepository, Request $request, EntityManagerInterface $entityManager, UpdateClimbRanks $updateClimbRanks): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $climb = $climbRepository->findOneBy(['id' => $climbId]);

        if (!$climb) {
            throw $this->createNotFoundException('Climb not found');
        }

        if (($climb->getClimber()->getId() !== $user->getId()) || !$this->isGranted('ROLE_DISCORD_MODERATOR')) {
            throw $this->createAccessDeniedException('You can not delete this climb');
        }

        $form = $this->createForm(ClimbType::class, $climb);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $climb->getRank()) {
                $ranked = true;
            } else {
                $ranked = false;
            }

            $entityManager->remove($climb);
            $entityManager->flush();

            if ($ranked) {
                $updateClimbRanks->updateClimbRanks($climb->getCategory(), $climb->getSubcategory());
            }

            return $this->redirectToRoute('climb_index');
        }

        return $this->render('climb/delete.html.twig', [
            'controller_name' => 'ClimbController',
            'form' => $form,
            'climb' => $climb,
        ]);
    }
}
