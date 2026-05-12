<?php

namespace App\Controller;

use App\Entity\RankMethod;
use App\Form\RankMethodType;
use App\Repository\RankMethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rank_method', name: 'rank_method_')]
final class RankMethodController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(RankMethodRepository $rankMethodRepository): Response
    {
        $user = $this->getUser();
        $rankMethods = $rankMethodRepository->findAll();

        return $this->render('rank_method/index.html.twig', [
            'controller_name' => 'RankMethodController',
            'user' => $user,
            'rankMethods' => $rankMethods,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_ADMIN');
        $user = $this->getUser();
        $rankMethod = new RankMethod();

        $form = $this->createForm(RankMethodType::class, $rankMethod);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rankMethod = $form->getData();
            $rankMethod->setCreatedAt(new \DateTimeImmutable());
            $rankMethod->setUpdatedAt(new \DateTime());

            $entityManager->persist($rankMethod);
            $entityManager->flush();

            return $this->redirectToRoute('rank_method_read', ['rankMethodId' => $rankMethod->getId()]);
        }

        return $this->render('rank_method/create.html.twig', [
            'controller_name' => 'RankMethodController',
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/read/{rankMethodId<\d+>}', name: 'read')]
    public function read(int $rankMethodId, RankMethodRepository $rankMethodRepository): Response
    {
        $user = $this->getUser();
        $rankMethod = $rankMethodRepository->findOneBy(['id' => $rankMethodId]);

        if (!$rankMethod) {
            throw $this->createNotFoundException('Rank Method not found');
        }

        return $this->render('rank_method/read.html.twig', [
            'controller_name' => 'RankMethodController',
            'user' => $user,
            'rankMethod' => $rankMethod,
        ]);
    }

    #[Route('/update/{rankMethodId<\d+>}', name: 'update')]
    public function update(int $rankMethodId, RankMethodRepository $rankMethodRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_ADMIN');
        $user = $this->getUser();
        $rankMethod = $rankMethodRepository->findOneBy(['id' => $rankMethodId]);

        if (!$rankMethod) {
            throw $this->createNotFoundException('Rank Method not found');
        }

        $form = $this->createForm(RankMethodType::class, $rankMethod);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rankMethod = $form->getData();
            $rankMethod->setUpdatedAt(new \DateTime());

            $entityManager->persist($rankMethod);
            $entityManager->flush();

            return $this->redirectToRoute('rank_method_read', ['rankMethodId' => $rankMethod->getId()]);
        }

        return $this->render('rank_method/update.html.twig', [
            'controller_name' => 'RankMethodController',
            'user' => $user,
            'form' => $form,
            'rankMethod' => $rankMethod,
        ]);
    }

    #[Route('/delete/{rankMethodId<\d+>}', name: 'delete')]
    public function delete(int $rankMethodId, RankMethodRepository $rankMethodRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISCORD_ADMIN');
        $user = $this->getUser();
        $rankMethod = $rankMethodRepository->findOneBy(['id' => $rankMethodId]);

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

        return $this->render('rank_method/delete.html.twig', [
            'controller_name' => 'RankMethodController',
            'user' => $user,
            'form' => $form,
            'rankMethod' => $rankMethod,
        ]);
    }
}
