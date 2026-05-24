<?php

namespace App\Controller;

use App\Entity\Climb;
use App\Enum\ClimbStatus;
use App\Form\ClimbType;
use App\Repository\CategoryRepository;
use App\Repository\ClimbRepository;
use App\Repository\SubcategoryRepository;
use App\Service\BreadcrumbsService;
use App\Service\UpdateClimbRanksService;
use App\Util\TimeFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/climb', name: 'climb_')]
final class ClimbController extends AbstractController
{
    #[Route('/{categoryId<\d+>?1}/{subcategoryId<\d+>?1}', name: 'index')]
    public function index(int $categoryId, int $subcategoryId, CategoryRepository $categoryRepository, SubcategoryRepository $subcategoryRepository, ClimbRepository $climbRepository, BreadcrumbsService $breadcrumbs): Response
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

        if ('Normal' === $subcategory->getName()) {
            $pageName = $category->getName();
        } else {
            $pageName = $subcategory->getName().' '.$category->getName();
        }

        $breadcrumbs
            ->addHome()
            ->addClimb()
            ->add($pageName, 'climb_index', ['categoryId' => $category->getId(), 'subcategoryId' => $subcategory->getId()])
        ;

        return $this->render('climb/index.html.twig', [
            'controller_name' => 'ClimbController',
            'categories' => $categories,
            'category' => $category,
            'subcategory' => $subcategory,
            'climbs' => $climbs,
            'breadcrumbs' => $breadcrumbs->all(),
            'pageName' => $pageName,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager, BreadcrumbsService $breadcrumbs): Response
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
            $climb->setStatus(ClimbStatus::UNREVIEWED);
            $climb->setRank(null);

            $entityManager->persist($climb);
            $entityManager->flush();

            return $this->redirectToRoute('climb_read', ['climbId' => $climb->getId()]);
        }

        $breadcrumbs
            ->addHome()
            ->addClimb()
            ->add('Create', 'climb_create')
        ;

        return $this->render('climb/create.html.twig', [
            'controller_name' => 'ClimbController',
            'user' => $user,
            'form' => $form,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/read/{climbId<\d+>}', name: 'read')]
    public function read(int $climbId, ClimbRepository $climbRepository, BreadcrumbsService $breadcrumbs): Response
    {
        $climb = $climbRepository->findOneBy(['id' => $climbId]);

        if (!$climb) {
            throw $this->createNotFoundException('Climb not found');
        }

        if ('Normal' === $climb->getSubcategory()->getName()) {
            $pageName = $climb->getCategory()->getName();
        } else {
            $pageName = $climb->getSubcategory()->getName().' '.$climb->getCategory()->getName();
        }

        $pageName = $pageName.' <span class="font-normal text-gray-700 text-sm">with a ';

        $rankMethod = $climb->getCategory()->getRankMethod()->getName();
        if (str_contains($rankMethod, 'Score')) {
            $pageName = $pageName.'score of</span> '.number_format($climb->getScore());
        } elseif (str_contains($rankMethod, 'Time')) {
            $pageName = $pageName.'time of</span> '.TimeFormatter::secondsToTime($climb->getTime());
        } elseif (str_contains($rankMethod, 'Height')) {
            $pageName = $pageName.'height of</span> '.number_format($climb->getHeight(), 2).' m';
        } elseif (str_contains($rankMethod, 'Speed')) {
            $pageName = $pageName.'speed of</span> '.number_format($climb->getSpeed(), 2).' m/s';
        }

        $pageName = $pageName.' <span class="font-normal text-gray-700 text-sm">by</span> '.$climb->getClimber()->getDisplayName();

        $breadcrumbs
            ->addHome()
            ->addClimb()
            ->add($pageName, 'climb_read', ['climbId' => $climbId])
        ;

        return $this->render('climb/read.html.twig', [
            'controller_name' => 'ClimbController',
            'climb' => $climb,
            'breadcrumbs' => $breadcrumbs->all(),
            'pageName' => $pageName,
        ]);
    }

    #[Route('/update/{climbId<\d+>}', name: 'update')]
    public function update(int $climbId, ClimbRepository $climbRepository, Request $request, EntityManagerInterface $entityManager, UpdateClimbRanksService $updateClimbRanks, BreadcrumbsService $breadcrumbs): Response
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
                $climb->setStatus(ClimbStatus::APPROVED);
                $climb->setVerifier($user);
                $climb->setIsReviewed(true);
                $approved = true;
            } elseif ($form->get('reject')->isClicked()) {
                $climb->setStatus(ClimbStatus::REJECTED);
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

        if ('Normal' === $climb->getSubcategory()->getName()) {
            $pageName = $climb->getCategory()->getName();
        } else {
            $pageName = $climb->getSubcategory()->getName().' '.$climb->getCategory()->getName();
        }

        $pageName = $pageName.' <span class="font-normal text-gray-700 text-sm">with a ';

        $rankMethod = $climb->getCategory()->getRankMethod()->getName();
        if (str_contains($rankMethod, 'Score')) {
            $pageName = $pageName.'score of</span> '.number_format($climb->getScore());
        } elseif (str_contains($rankMethod, 'Time')) {
            $pageName = $pageName.'time of</span> '.TimeFormatter::secondsToTime($climb->getTime());
        } elseif (str_contains($rankMethod, 'Height')) {
            $pageName = $pageName.'height of</span> '.number_format($climb->getHeight(), 2).' m';
        } elseif (str_contains($rankMethod, 'Speed')) {
            $pageName = $pageName.'speed of</span> '.number_format($climb->getSpeed(), 2).' m/s';
        }

        $pageName = $pageName.' <span class="font-normal text-gray-700 text-sm">by</span> '.$climb->getClimber()->getDisplayName();

        $breadcrumbs
            ->addHome()
            ->addClimb()
            ->add($pageName, 'climb_read', ['climbId' => $climbId])
            ->add('Update', 'climb_update', ['climbId' => $climbId])
        ;

        return $this->render('climb/update.html.twig', [
            'controller_name' => 'ClimbController',
            'user' => $user,
            'form' => $form,
            'climb' => $climb,
            'breadcrumbs' => $breadcrumbs->all(),
            'pageName' => $pageName,
        ]);
    }

    #[Route('/delete/{climbId<\d+>}', name: 'delete')]
    public function delete(int $climbId, ClimbRepository $climbRepository, Request $request, EntityManagerInterface $entityManager, UpdateClimbRanksService $updateClimbRanks, BreadcrumbsService $breadcrumbs): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $climb = $climbRepository->findOneBy(['id' => $climbId]);

        if (!$climb) {
            throw $this->createNotFoundException('Climb not found');
        }

        if (($climb->getClimber()->getId() !== $user->getId()) && !$this->isGranted('ROLE_DISCORD_MODERATOR')) {
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

        if ('Normal' === $climb->getSubcategory()->getName()) {
            $pageName = $climb->getCategory()->getName();
        } else {
            $pageName = $climb->getSubcategory()->getName().' '.$climb->getCategory()->getName();
        }

        $pageName = $pageName.' <span class="font-normal text-gray-700 text-sm">with a ';

        $rankMethod = $climb->getCategory()->getRankMethod()->getName();
        if (str_contains($rankMethod, 'Score')) {
            $pageName = $pageName.'score of</span> '.number_format($climb->getScore());
        } elseif (str_contains($rankMethod, 'Time')) {
            $pageName = $pageName.'time of</span> '.TimeFormatter::secondsToTime($climb->getTime());
        } elseif (str_contains($rankMethod, 'Height')) {
            $pageName = $pageName.'height of</span> '.number_format($climb->getHeight(), 2).' m';
        } elseif (str_contains($rankMethod, 'Speed')) {
            $pageName = $pageName.'speed of</span> '.number_format($climb->getSpeed(), 2).' m/s';
        }

        $pageName = $pageName.' <span class="font-normal text-gray-700 text-sm">by</span> '.$climb->getClimber()->getDisplayName();

        $breadcrumbs
            ->addHome()
            ->addClimb()
            ->add($pageName, 'climb_read', ['climbId' => $climbId])
            ->add('Delete', 'climb_delete', ['climbId' => $climbId])
        ;

        return $this->render('climb/delete.html.twig', [
            'controller_name' => 'ClimbController',
            'form' => $form,
            'climb' => $climb,
            'breadcrumbs' => $breadcrumbs->all(),
            'pageName' => $pageName,
        ]);
    }
}
