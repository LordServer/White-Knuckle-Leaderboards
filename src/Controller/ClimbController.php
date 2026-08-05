<?php

namespace App\Controller;

use App\Entity\Climb;
use App\Enum\ClimbStatus;
use App\Form\ClimbType;
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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/climb', name: 'climb_')]
final class ClimbController extends AbstractController
{
    #[Route('/c{categoryId<\d+>?1}s{subcategoryId<\d+>?1}', name: 'index')]
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
        $climbs->setMaxPerPage($request->query->get('perPage', 50));
        $climbs->setCurrentPage($request->query->get('page', 1));

        $pagination = $paginationService->build(
            currentPage: $request->query->get('page', 1),
            totalPages: $climbs->getNbPages(),
            totalResults: $climbs->getNbResults(),
            maxPerPage: $request->query->get('perPage', 50)
        );

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
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'create')]
    #[IsGranted(ClimbVoter::CREATE)]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        BreadcrumbsService $breadcrumbs,
        ClimbName $climbName,
    ): Response {
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

            $pageName = $climbName->pageName($climb);
            flash()
                ->use('theme.ruby')
                ->success('Climb submitted!');

            return $this->redirectToRoute('climb_read', ['climbId' => $climb->getId()]);
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                flash()
                    ->use('theme.ruby')
                    ->error($error->getMessage());
            }
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

    #[Route('/{climbId<\d+>}', name: 'read')]
    public function read(
        int $climbId,
        ClimbRepository $climbRepository,
        BreadcrumbsService $breadcrumbs,
        ClimbName $climbName,
    ): Response {
        $climb = $climbRepository->findOneBy(['id' => $climbId]);

        if (!$climb) {
            throw $this->createNotFoundException('Climb not found');
        }

        $pageName = $climbName->pageName($climb);

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

    #[Route('/{climbId<\d+>}/edit', name: 'update')]
    public function update(
        int $climbId,
        ClimbRepository $climbRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        BreadcrumbsService $breadcrumbs,
        ClimbName $climbName,
    ): Response {
        $climb = $climbRepository->findOneBy(['id' => $climbId]);

        $this->denyAccessUnlessGranted(ClimbVoter::UPDATE, $climb);

        $pageName = $climbName->pageName($climb);

        if (!$climb) {
            throw $this->createNotFoundException('Climb not found');
        }

        $form = $this->createForm(ClimbType::class, $climb);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $climb = $form->getData();

            $entityManager->persist($climb);
            $entityManager->flush();
            flash()
                ->use('theme.ruby')
                ->success("{$pageName} updated!");

            return $this->redirectToRoute('climb_read', ['climbId' => $climb->getId()]);
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

        return $this->render('climb/update.html.twig', [
            'controller_name' => 'ClimbController',
            'form' => $form,
            'climb' => $climb,
            'breadcrumbs' => $breadcrumbs->all(),
            'pageName' => $pageName,
        ]);
    }

    #[Route('/{climbId<\d+>}/delete', name: 'delete')]
    public function delete(
        int $climbId,
        ClimbRepository $climbRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        UpdateClimbRanksService $updateClimbRanks,
        BreadcrumbsService $breadcrumbs,
        ClimbName $climbName,
    ): Response {
        $climb = $climbRepository->findOneBy(['id' => $climbId]);

        $this->denyAccessUnlessGranted(ClimbVoter::DELETE, $climb);

        if (!$climb) {
            throw $this->createNotFoundException('Climb not found');
        }

        $pageName = $climbName->pageName($climb);

        $form = $this->createForm(ClimbType::class, $climb, ['delete' => true]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $climb->getRank()) {
                $ranked = true;
            } else {
                $ranked = false;
            }

            $entityManager->remove($climb);
            $entityManager->flush();
            flash()
                ->use('theme.ruby')
                ->success("{$pageName} deleted!");

            if ($ranked) {
                $updateClimbRanks->updateClimbRanks($climb->getCategory(), $climb->getSubcategory());
            }

            return $this->redirectToRoute('climb_index');
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors() as $error) {
                flash()->use('theme.ruby')->error($error->getMessage());
            }
        }

        $breadcrumbs
            ->addHome()
            ->addClimb()
            ->add($pageName, 'climb_read', ['climbId' => $climbId])
            ->add('Delete', 'climb_delete', ['climbId' => $climbId])
        ;

        // TODO: Add more info to delete page

        return $this->render('climb/delete.html.twig', [
            'controller_name' => 'ClimbController',
            'form' => $form,
            'climb' => $climb,
            'breadcrumbs' => $breadcrumbs->all(),
            'pageName' => $pageName,
        ]);
    }
}
