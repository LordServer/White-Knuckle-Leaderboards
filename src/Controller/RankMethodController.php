<?php

namespace App\Controller;

use App\Entity\RankMethod;
use App\Form\RankMethodType;
use App\Repository\RankMethodRepository;
use App\Security\Voter\RankMethodVoter;
use App\Service\BreadcrumbsService;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/rank_method', name: 'rank_method_')]
final class RankMethodController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        RankMethodRepository $rankMethodRepository,
        BreadcrumbsService $breadcrumbs,
        Request $request,
        PaginationService $paginationService,
    ): Response {
        $rankMethods = $rankMethodRepository->findAllOrderedByIndex();
        $rankMethods->setMaxPerPage($request->query->get('perPage', 50));
        $rankMethods->setCurrentPage($request->query->get('page', 1));

        $pagination = $paginationService->build(
            currentPage: $request->query->get('page', 1),
            totalPages: $rankMethods->getNbPages(),
            totalResults: $rankMethods->getNbResults(),
            maxPerPage: $request->query->get('perPage', 50)
        );

        $breadcrumbs
            ->addHome()
            ->addRankMethod()
        ;

        return $this->render('rank_method/index.html.twig', [
            'controller_name' => 'RankMethodController',
            'rankMethods' => $rankMethods,
            'breadcrumbs' => $breadcrumbs->all(),
            'pagination' => $pagination,
        ]);
    }

    #[Route('/create', name: 'create')]
    #[IsGranted(RankMethodVoter::CREATE)]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        BreadcrumbsService $breadcrumbs,
    ): Response {
        $rankMethod = new RankMethod();

        $form = $this->createForm(RankMethodType::class, $rankMethod);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rankMethod = $form->getData();

            $entityManager->persist($rankMethod);
            $entityManager->flush();

            return $this->redirectToRoute('rank_method_read', ['rankMethodId' => $rankMethod->getId()]);
        }

        $breadcrumbs
            ->addHome()
            ->addRankMethod()
            ->add('Create', 'rank_method_create')
        ;

        return $this->render('rank_method/create.html.twig', [
            'controller_name' => 'RankMethodController',
            'form' => $form,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/read/{rankMethodId<\d+>}', name: 'read')]
    public function read(
        int $rankMethodId,
        RankMethodRepository $rankMethodRepository,
        BreadcrumbsService $breadcrumbs,
    ): Response {
        $rankMethod = $rankMethodRepository->findOneBy(['id' => $rankMethodId]);

        if (!$rankMethod) {
            throw $this->createNotFoundException('Rank Method not found');
        }

        $breadcrumbs
            ->addHome()
            ->addRankMethod()
            ->add($rankMethod->getName(), 'rank_method_read', ['rankMethodId' => $rankMethodId])
        ;

        return $this->render('rank_method/read.html.twig', [
            'controller_name' => 'RankMethodController',
            'rankMethod' => $rankMethod,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/update/{rankMethodId<\d+>}', name: 'update')]
    public function update(
        int $rankMethodId,
        RankMethodRepository $rankMethodRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        BreadcrumbsService $breadcrumbs,
    ): Response {
        $rankMethod = $rankMethodRepository->findOneBy(['id' => $rankMethodId]);

        $this->denyAccessUnlessGranted(RankMethodVoter::UPDATE, $rankMethod);

        if (!$rankMethod) {
            throw $this->createNotFoundException('Rank Method not found');
        }

        $form = $this->createForm(RankMethodType::class, $rankMethod);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rankMethod = $form->getData();

            $entityManager->persist($rankMethod);
            $entityManager->flush();

            return $this->redirectToRoute('rank_method_read', ['rankMethodId' => $rankMethod->getId()]);
        }

        $breadcrumbs
            ->addHome()
            ->addRankMethod()
            ->add($rankMethod->getName(), 'rank_method_read', ['rankMethodId' => $rankMethodId])
            ->add('Update', 'rank_method_update', ['rankMethodId' => $rankMethodId])
        ;

        return $this->render('rank_method/update.html.twig', [
            'controller_name' => 'RankMethodController',
            'form' => $form,
            'rankMethod' => $rankMethod,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/delete/{rankMethodId<\d+>}', name: 'delete')]
    public function delete(
        int $rankMethodId,
        RankMethodRepository $rankMethodRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        BreadcrumbsService $breadcrumbs,
    ): Response {
        $rankMethod = $rankMethodRepository->findOneBy(['id' => $rankMethodId]);

        $this->denyAccessUnlessGranted(RankMethodVoter::DELETE, $rankMethod);

        if (!$rankMethod) {
            throw $this->createNotFoundException('Rank Method not found');
        }

        $form = $this->createForm(RankMethodType::class, $rankMethod);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->remove($rankMethod);
            $entityManager->flush();

            return $this->redirectToRoute('rank_method_index');
        }

        $breadcrumbs
            ->addHome()
            ->addRankMethod()
            ->add($rankMethod->getName(), 'rank_method_read', ['rankMethodId' => $rankMethodId])
            ->add('Delete', 'rank_method_delete', ['rankMethodId' => $rankMethodId])
        ;

        return $this->render('rank_method/delete.html.twig', [
            'controller_name' => 'RankMethodController',
            'form' => $form,
            'rankMethod' => $rankMethod,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }
}
