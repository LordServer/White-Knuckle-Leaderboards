<?php

namespace App\Controller;

use App\Enum\ClimbStatus;
use App\Form\ApprovalType;
use App\Repository\CategoryRepository;
use App\Repository\ClimbRepository;
use App\Repository\SubcategoryRepository;
use App\Security\ClimbVoter;
use App\Service\BreadcrumbsService;
use App\Service\UpdateClimbRanksService;
use App\Util\TimeFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/approval', name: 'approval_')]
final class ApprovalController extends AbstractController
{
    #[Route('/{categoryId<\d+>?1}/{subcategoryId<\d+>?1}', name: 'index')]
    public function index(int $categoryId, int $subcategoryId, CategoryRepository $categoryRepository, SubcategoryRepository $subcategoryRepository, ClimbRepository $climbRepository, BreadcrumbsService $breadcrumbs): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_AUTHORIZER');

        $approvalBreakdown = $climbRepository->getUnreviewedBreakdown();

        $categoryExists = in_array($categoryId, array_column($approvalBreakdown, 'id'));
        if (!$categoryExists) {
            $category = $categoryRepository->findOneBy(['id' => array_key_first($approvalBreakdown)]);
        } else {
            $category = $categoryRepository->findOneBy(['id' => $categoryId]);
        }

        $subcategoryExists = in_array($subcategoryId, array_column($approvalBreakdown[$category->getId()]['subcategories'], 'id'));
        if (!$subcategoryExists) {
            $subcategory = $subcategoryRepository->findOneBy(['id' => array_key_first($approvalBreakdown)]);
        } else {
            $subcategory = $subcategoryRepository->findOneBy(['id' => $subcategoryId]);
        }

        $climbs = $climbRepository->findByCategoryAndSubcategoryAndApprovalStatusSortByOldestCreatedAt($category, $subcategory, false);

        if ('Normal' === $subcategory->getName()) {
            $pageName = $category->getName();
        } else {
            $pageName = $subcategory->getName().' '.$category->getName();
        }

        $breadcrumbs
            ->addHome()
            ->addApproval()
            ->add($pageName, 'approval_index', ['categoryId' => $category->getId(), 'subcategoryId' => $subcategory->getId()])
        ;

        return $this->render('approval/index.html.twig', [
            'controller_name' => 'ApprovalController',
            'category' => $category,
            'subcategory' => $subcategory,
            'climbs' => $climbs,
            'approvalBreakdown' => $approvalBreakdown,
            'breadcrumbs' => $breadcrumbs->all(),
            'pageName' => $pageName,
        ]);
    }

    #[Route('/review/{climbId<\d+>}', name: 'review')]
    public function review(int $climbId, ClimbRepository $climbRepository, Request $request, EntityManagerInterface $entityManager, UpdateClimbRanksService $updateClimbRanks, BreadcrumbsService $breadcrumbs): Response
    {
        $climb = $climbRepository->findOneBy(['id' => $climbId]);

        $this->denyAccessUnlessGranted(ClimbVoter::AUTHORIZE, $climb);
        $user = $this->getUser();

        if (!$climb) {
            throw $this->createNotFoundException('Climb not found');
        }

        $form = $this->createForm(ApprovalType::class, $climb);

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
                $approved = false;
            }

            $entityManager->persist($climb);
            $entityManager->flush();

            if ($approved) {
                $updateClimbRanks->updateClimbRanks($climb->getCategory(), $climb->getSubcategory());
            }

            return $this->redirectToRoute('approval_index');
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

        return $this->render('approval/approval.html.twig', [
            'controller_name' => 'ClimbController',
            'form' => $form,
            'climb' => $climb,
            'breadcrumbs' => $breadcrumbs->all(),
            'pageName' => $pageName,
        ]);
    }
}
