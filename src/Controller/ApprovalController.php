<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ClimbRepository;
use App\Repository\SubcategoryRepository;
use App\Service\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/approval', name: 'approval_')]
final class ApprovalController extends AbstractController
{
    #[Route('/{categoryId<\d+>?1}/{subcategoryId<\d+>?1}', name: 'index')]
    public function index(int $categoryId, int $subcategoryId, CategoryRepository $categoryRepository, SubcategoryRepository $subcategoryRepository, ClimbRepository $climbRepository, Breadcrumbs $breadcrumbs): Response
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
}
