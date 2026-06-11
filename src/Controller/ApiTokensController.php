<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Form\ApiTokenType;
use App\Repository\ApiTokenRepository;
use App\Repository\UserRepository;
use App\Security\Voter\ApiTokenVoter;
use App\Service\BreadcrumbsService;
use App\Service\PaginationService;
use App\Service\ScopePermissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/climber/api-tokens', name: 'api_tokens_')]
final class ApiTokensController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function apiTokens(ApiTokenRepository $apiTokenRepository, BreadcrumbsService $breadcrumbs, Request $request, PaginationService $paginationService, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
            // TODO: Finish API Token Breadcrumb
        ;

        return $this->render('api_tokens/index.html.twig', [
            'controller_name' => 'ApiTokensController',
            'apiTokens' => $apiTokens,
            'breadcrumbs' => $breadcrumbs->all(),
            'pagination' => $pagination,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function apiTokensCreate(ScopePermissionService $scopesManager): Response
    {
        $apiToken = new ApiToken();

        $this->denyAccessUnlessGranted(ApiTokenVoter::CREATE, $apiToken);

        $apiScopes = $scopesManager->getAssignableScopes();

        $form = $this->createForm(ApiTokenType::class, $apiToken);

        return $this->render('api_tokens/create.html.twig', [
            'controller_name' => 'ApiTokensController',
            'apiScopes' => $apiScopes,
            'form' => $form,
        ]);
    }

    #[Route('/{apiTokenId<\d+>}', name: 'read')]
    public function apiTokensRead(int $apiTokenId, ApiTokenRepository $apiTokenRepository): Response
    {
        $apiToken = $apiTokenRepository->findOneBy(['id' => $apiTokenId]);

        $this->denyAccessUnlessGranted(ApiTokenVoter::READ, $apiToken);

        return $this->render('api_tokens/read.html.twig', [
            'controller_name' => 'ApiTokensController',
            'apiToken' => $apiToken,
        ]);
    }

    #[Route('/{apiTokenId<\d+>}/update', name: 'update')]
    public function apiTokensUpdate(int $apiTokenId, ApiTokenRepository $apiTokenRepository): Response
    {
        $apiToken = $apiTokenRepository->findOneBy(['id' => $apiTokenId]);

        $this->denyAccessUnlessGranted(ApiTokenVoter::UPDATE, $apiToken);

        $form = $this->createForm(ApiTokenType::class, $apiToken);

        return $this->render('api_tokens/update.html.twig', [
            'controller_name' => 'ApiTokensController',
            'form' => $form,
        ]);
    }

    #[Route('/{apiTokenId<\d+>}/delete', name: 'delete')]
    public function apiTokensDelete(int $apiTokenId, ApiTokenRepository $apiTokenRepository): Response
    {
        $apiToken = $apiTokenRepository->findOneBy(['id' => $apiTokenId]);

        $this->denyAccessUnlessGranted(ApiTokenVoter::UPDATE, $apiToken);

        return $this->render('api_tokens/delete.html.twig', [
            'controller_name' => 'ApiTokensController',
        ]);
    }
}
