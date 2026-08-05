<?php

namespace App\Controller;

use App\Enum\ClimbStatus;
use App\Form\ApprovalType;
use App\Repository\CategoryRepository;
use App\Repository\ClimbRepository;
use App\Repository\SubcategoryRepository;
use App\Security\Voter\ClimbVoter;
use App\Service\BreadcrumbsService;
use App\Service\PaginationService;
use App\Service\UpdateClimbRanksService;
use App\Util\ClimbName;
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
    public function index(
        int $categoryId,
        int $subcategoryId,
        CategoryRepository $categoryRepository,
        SubcategoryRepository $subcategoryRepository,
        ClimbRepository $climbRepository,
        BreadcrumbsService $breadcrumbs,
        Request $request,
        PaginationService $paginationService,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_AUTHORIZER');

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
        $climbs->setMaxPerPage($request->query->get('perPage', 50));
        $climbs->setCurrentPage($request->query->get('page', 1));

        $pagination = $paginationService->build(currentPage: $request->query->get('page', 1), totalPages: $climbs->getNbPages(), totalResults: $climbs->getNbResults(), maxPerPage: $request->query->get('perPage', 50));

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
            'pagination' => $pagination,
        ]);
    }

    #[Route('/{climbId<\d+>}/review', name: 'review')]
    public function review(
        int $climbId,
        ClimbRepository $climbRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        UpdateClimbRanksService $updateClimbRanks,
        BreadcrumbsService $breadcrumbs,
        ClimbName $climbName,
    ): Response {
        $climb = $climbRepository->findOneBy(['id' => $climbId]);

        $this->denyAccessUnlessGranted(ClimbVoter::AUTHORIZE, $climb);
        $user = $this->getUser();

        if (!$climb) {
            throw $this->createNotFoundException('Climb not found');
        }

        $pageName = $climbName->pageName($climb);

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
                flash()->use('theme.ruby')->success("{$pageName} approved.");
            } elseif ($form->get('reject')->isClicked()) {
                $climb->setStatus(ClimbStatus::REJECTED);
                $climb->setVerifier($user);
                $climb->setIsReviewed(true);
                $approved = false;
                flash()->use('theme.ruby')->success("{$pageName} rejected.");
            }

            $entityManager->persist($climb);
            $entityManager->flush();

            if ($approved) {
                $updateClimbRanks->updateClimbRanks($climb->getCategory(), $climb->getSubcategory());
            }

            return $this->redirectToRoute('approval_index');
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors() as $error) {
                flash()->use('theme.ruby')->error($error->getMessage());
            }
        }

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
