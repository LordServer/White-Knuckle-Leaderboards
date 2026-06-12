<?php

namespace App\Controller;

use App\Enum\UserStatus;
use App\Form\AdminType;
use App\Repository\UserRepository;
use App\Service\BreadcrumbsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin/climber/{climberId<\d+>}/moderate', name: 'admin_user_moderate')]
    public function moderate(
        int $climberId,
        Request $request,
        UserRepository $climberRepository,
        EntityManagerInterface $entityManager,
        BreadcrumbsService $breadcrumbs,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_AUTHORIZER');

        $climber = $climberRepository->findOneBy(['id' => $climberId]);

        if (!$climber) {
            throw $this->createNotFoundException('User not found');
        }

        $form = $this->createForm(AdminType::class, $climber);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime('now');
            if (null !== $form->get('banDays')->getData()) {
                $date->modify("+{$form->get('banDays')->getData()} days");
                $climber->setBannedUntil($date);
            } else {
                $climber->setBannedUntil(null);
            }
            $entityManager->persist($climber);
            $entityManager->flush();
        }

        $breadcrumbs
            ->addHome()
            ->addClimber()
            ->add($climber->getDisplayName(), 'user_read', ['userId' => $climberId])
            ->add('Moderate', 'admin_user_moderate', ['climberId' => $climberId])
        ;

        // TODO: Add actual user information to page, like username and display name
        return $this->render('admin/moderate.html.twig', [
            'controller_name' => 'AdminController',
            'form' => $form,
            'climber' => $climber,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }
}
