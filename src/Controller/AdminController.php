<?php

namespace App\Controller;

use App\Form\AdminType;
use App\Repository\UserRepository;
use App\Service\RolePermissionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin/climber/{climberId<\d+>}/moderate', name: 'admin_user_moderate')]
    public function moderate(int $climberId, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, RolePermissionService $roleManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_AUTHORIZER');
        $user = $userRepository->findOneBy(['id' => $climberId]);

        $form = $this->createForm(AdminType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $roleManager->updateRoles(
                $user,
                $form->get('roles')->getData()
            );
            $date = new \DateTime('now');
            if (null !== $form->get('banDays')->getData()) {
                $date->modify("+{$form->get('banDays')->getData()} days");
                $user->setBannedUntil($date);
            } else {
                $user->setBannedUntil(null);
            }
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('admin/moderate.html.twig', [
            'controller_name' => 'AdminController',
            'form' => $form,
            'user' => $user,
        ]);
    }
}
