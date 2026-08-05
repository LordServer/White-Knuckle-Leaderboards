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

            if (UserStatus::ACTIVE === $form->get('status')->getData()) {
                flash()->use('theme.ruby')->success("{$climber->getDisplayName()} unbanned.");
            } elseif (UserStatus::SUSPENDED === $form->get('status')->getData()) {
                flash()->use('theme.ruby')->success("{$climber->getDisplayName()} suspended for {$form->get('banDays')->getData()} day(s).");
            } elseif (UserStatus::BANNED === $form->get('status')->getData()) {
                flash()->use('theme.ruby')->success("{$climber->getDisplayName()} banned permanently.");
            } else {
                flash()->use('theme.ruby')->error('Shits broke, yo.');
                dd($form->get('status')->getData());
            }

            $entityManager->persist($climber);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors() as $error) {
                flash()->use('theme.ruby')->error($error->getMessage());
            }
        }

        $breadcrumbs
            ->addHome()
            ->addClimber()
            ->add($climber->getDisplayName(), 'user_read', ['userId' => $climberId])
            ->add('Moderate', 'admin_user_moderate', ['climberId' => $climberId])
        ;

        return $this->render('admin/moderate.html.twig', [
            'controller_name' => 'AdminController',
            'form' => $form,
            'climber' => $climber,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }
}
