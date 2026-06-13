<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Form\ApiTokenType;
use App\Repository\ApiTokenRepository;
use App\Security\Voter\ApiTokenVoter;
use App\Service\BreadcrumbsService;
use App\Service\PaginationService;
use App\Service\ScopePermissionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/climber/api-tokens', name: 'api_tokens_')]
final class ApiTokensController extends AbstractController
{
    #[Route('', name: 'index')]
    #[IsGranted(ApiTokenVoter::LIST)]
    public function apiTokens(
        ApiTokenRepository $apiTokenRepository,
        BreadcrumbsService $breadcrumbs,
        Request $request,
        PaginationService $paginationService,
    ): Response {
        $apiTokens = $apiTokenRepository->findByOwnerOrderByIndex($this->getUser());
        $apiTokens->setMaxPerPage($request->query->get('perPage', 50));
        $apiTokens->setCurrentPage($request->query->get('page', 1));

        $pagination = $paginationService->build(
            currentPage: $request->query->get('page', 1),
            totalPages: $apiTokens->getNbPages(),
            totalResults: $apiTokens->getNbResults(),
            maxPerPage: $request->query->get('perPage', 50)
        );

        $breadcrumbs
            ->addHome()
            ->addClimber()
            ->add($this->getUser()->getDisplayName(), 'user_read', ['userId' => $this->getUser()->getId()])
            ->addApiTokens()
        ;

        return $this->render('api_tokens/index.html.twig', [
            'controller_name' => 'ApiTokensController',
            'apiTokens' => $apiTokens,
            'breadcrumbs' => $breadcrumbs->all(),
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'create')]
    #[IsGranted(ApiTokenVoter::CREATE)]
    public function apiTokensCreate(
        ScopePermissionService $scopesManager,
        BreadcrumbsService $breadcrumbs,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $apiToken = new ApiToken();

        $user = $this->getUser();
        $apiScopes = $scopesManager->getAssignableScopes();

        $form = $this->createForm(ApiTokenType::class, $apiToken);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $scopesManager->assignScopes(
                $form->get('scopes')->getData(),
            );
            if ($form->get('neverExpires')->getData()) {
                $this->denyAccessUnlessGranted('ROLE_ADMIN');
            }
            $apiToken = $form->getData();
            $apiToken->setOwnedBy($user);

            $entityManager->persist($apiToken);
            $entityManager->flush();

            return $this->redirectToRoute('api_tokens_read', ['apiTokenId' => $apiToken->getId()]);
        }

        $breadcrumbs
            ->addHome()
            ->addClimber()
            ->add($this->getUser()->getDisplayName(), 'user_read', ['userId' => $this->getUser()->getId()])
            ->addApiTokens()
            ->add('New', 'api_tokens_create')
        ;

        return $this->render('api_tokens/create.html.twig', [
            'controller_name' => 'ApiTokensController',
            'apiScopes' => $apiScopes,
            'form' => $form,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/{apiTokenId<\d+>}', name: 'read')]
    public function apiTokensRead(
        int $apiTokenId,
        ApiTokenRepository $apiTokenRepository,
        BreadcrumbsService $breadcrumbs,
    ): Response {
        $apiToken = $apiTokenRepository->findOneBy(['id' => $apiTokenId]);

        $this->denyAccessUnlessGranted(ApiTokenVoter::READ, $apiToken);

        if (!$apiToken) {
            throw $this->createNotFoundException('API Token not found');
        }

        $tokenName = 'Token '.substr($apiToken->getToken(), 4, 12);

        $breadcrumbs
            ->addHome()
            ->addClimber()
            ->add($this->getUser()->getDisplayName(), 'user_read', ['userId' => $this->getUser()->getId()])
            ->addApiTokens()
            ->add($tokenName, 'api_tokens_read', ['apiTokenId' => $apiTokenId])
        ;

        return $this->render('api_tokens/read.html.twig', [
            'controller_name' => 'ApiTokensController',
            'apiToken' => $apiToken,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/{apiTokenId<\d+>}/edit', name: 'update')]
    public function apiTokensUpdate(
        int $apiTokenId,
        ApiTokenRepository $apiTokenRepository,
        BreadcrumbsService $breadcrumbs,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $apiToken = $apiTokenRepository->findOneBy(['id' => $apiTokenId]);

        $this->denyAccessUnlessGranted(ApiTokenVoter::UPDATE, $apiToken);

        if (!$apiToken) {
            throw $this->createNotFoundException('API Token not found');
        }

        $form = $this->createForm(ApiTokenType::class, $apiToken);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $apiToken = $form->getData();

            $entityManager->persist($apiToken);
            $entityManager->flush();

            return $this->redirectToRoute('api_tokens_read', ['apiTokenId' => $apiToken->getId()]);
        }

        $tokenName = 'Token '.substr($apiToken->getToken(), 4, 12);

        $breadcrumbs
            ->addHome()
            ->addClimber()
            ->add($this->getUser()->getDisplayName(), 'user_read', ['userId' => $this->getUser()->getId()])
            ->addApiTokens()
            ->add($tokenName, 'api_tokens_read', ['apiTokenId' => $apiTokenId])
            ->add('Update', 'api_tokens_update', ['apiTokenId' => $apiTokenId])
        ;

        return $this->render('api_tokens/update.html.twig', [
            'controller_name' => 'ApiTokensController',
            'apiToken' => $apiToken,
            'form' => $form,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/{apiTokenId<\d+>}/delete', name: 'delete')]
    public function apiTokensDelete(
        int $apiTokenId,
        ApiTokenRepository $apiTokenRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        BreadcrumbsService $breadcrumbs,
    ): Response {
        $apiToken = $apiTokenRepository->findOneBy(['id' => $apiTokenId]);

        $this->denyAccessUnlessGranted(ApiTokenVoter::DELETE, $apiToken);

        if (!$apiToken) {
            throw $this->createNotFoundException('API Token not found');
        }

        $form = $this->createForm(ApiTokenType::class, $apiToken, ['delete' => true]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->remove($apiToken);
            $entityManager->flush();

            return $this->redirectToRoute('api_tokens_index');
        }

        $tokenName = 'Token '.substr($apiToken->getToken(), 4, 12);

        $breadcrumbs
            ->addHome()
            ->addClimber()
            ->add($this->getUser()->getDisplayName(), 'user_read', ['userId' => $this->getUser()->getId()])
            ->addApiTokens()
            ->add($tokenName, 'api_tokens_read', ['apiTokenId' => $apiTokenId])
            ->add('Delete', 'api_tokens_delete', ['apiTokenId' => $apiTokenId])
        ;

        return $this->render('api_tokens/delete.html.twig', [
            'controller_name' => 'ApiTokensController',
            'apiToken' => $apiToken,
            'form' => $form,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }
}
