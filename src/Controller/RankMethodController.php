<?php

namespace App\Controller;

use App\Entity\RankMethod;
use App\Form\RankMethodType;
use App\Repository\RankMethodRepository;
use App\Security\RankMethodVoter;
use App\Service\BreadcrumbsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rank_method', name: 'rank_method_')]
final class RankMethodController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(RankMethodRepository $rankMethodRepository, BreadcrumbsService $breadcrumbs): Response
    {
        $rankMethods = $rankMethodRepository->findAll();

        $breadcrumbs
            ->addHome()
            ->addRankMethod()
        ;

        return $this->render('rank_method/index.html.twig', [
            'controller_name' => 'RankMethodController',
            'rankMethods' => $rankMethods,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager, BreadcrumbsService $breadcrumbs): Response
    {
        $rankMethod = new RankMethod();

        $this->denyAccessUnlessGranted(RankMethodVoter::CREATE, $rankMethod);

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
    public function read(int $rankMethodId, RankMethodRepository $rankMethodRepository, BreadcrumbsService $breadcrumbs): Response
    {
        $rankMethod = $rankMethodRepository->findOneBy(['id' => $rankMethodId]);

        if (!$rankMethod) {
            throw $this->createNotFoundException('Rank Method not found');
        }

        // TODO: Add categories that use this rank method

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
    public function update(int $rankMethodId, RankMethodRepository $rankMethodRepository, Request $request, EntityManagerInterface $entityManager, BreadcrumbsService $breadcrumbs): Response
    {
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
    public function delete(int $rankMethodId, RankMethodRepository $rankMethodRepository, Request $request, EntityManagerInterface $entityManager, BreadcrumbsService $breadcrumbs): Response
    {
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
