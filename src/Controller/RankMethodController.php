<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rank_method', name: 'rank_method_')]
final class RankMethodController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('rank_method/index.html.twig', [
            'controller_name' => 'RankMethodController',
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(): Response
    {
        return $this->render('rank_method/create.html.twig', [
            'controller_name' => 'RankMethodController',
        ]);
    }

    #[Route('/read', name: 'read')]
    public function read(): Response
    {
        return $this->render('rank_method/read.html.twig', [
            'controller_name' => 'RankMethodController',
        ]);
    }

    #[Route('/update', name: 'update')]
    public function update(): Response
    {
        return $this->render('rank_method/update.html.twig', [
            'controller_name' => 'RankMethodController',
        ]);
    }

    #[Route('/delete', name: 'delete')]
    public function delete(): Response
    {
        return $this->render('rank_method/delete.html.twig', [
            'controller_name' => 'RankMethodController',
        ]);
    }
}
