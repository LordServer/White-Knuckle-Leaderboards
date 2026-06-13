<?php

namespace App\Controller;

use App\Entity\Subcategory;
use App\Form\SubcategoryType;
use App\Repository\SubcategoryRepository;
use App\Security\Voter\SubcategoryVoter;
use App\Service\BreadcrumbsService;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/subcategory', name: 'subcategory_')]
final class SubcategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        SubcategoryRepository $subcategoryRepository,
        BreadcrumbsService $breadcrumbs,
        Request $request,
        PaginationService $paginationService,
    ): Response {
        $subcategories = $subcategoryRepository->findAllOrderedByIndex();
        $subcategories->setMaxPerPage($request->query->get('perPage', 50));
        $subcategories->setCurrentPage($request->query->get('page', 1));

        $pagination = $paginationService->build(
            currentPage: $request->query->get('page', 1),
            totalPages: $subcategories->getNbPages(),
            totalResults: $subcategories->getNbResults(),
            maxPerPage: $request->query->get('perPage', 50)
        );

        $breadcrumbs
            ->addHome()
            ->addSubcategory()
        ;

        return $this->render('subcategory/index.html.twig', [
            'controller_name' => 'SubcategoryController',
            'subcategories' => $subcategories,
            'breadcrumbs' => $breadcrumbs->all(),
            'pagination' => $pagination,
        ]);
    }

    #[Route('/create', name: 'create')]
    #[IsGranted(SubcategoryVoter::CREATE)]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        BreadcrumbsService $breadcrumbs,
    ): Response {
        $subcategory = new Subcategory();

        $form = $this->createForm(SubcategoryType::class, $subcategory);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $subcategory = $form->getData();

            $entityManager->persist($subcategory);
            $entityManager->flush();

            return $this->redirectToRoute('subcategory_read', ['subcategoryId' => $subcategory->getId()]);
        }

        $breadcrumbs
            ->addHome()
            ->addSubcategory()
            ->add('Create', 'subcategory_create')
        ;

        return $this->render('subcategory/create.html.twig', [
            'controller_name' => 'SubcategoryController',
            'form' => $form,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/read/{subcategoryId<\d+>}', name: 'read')]
    public function read(
        int $subcategoryId,
        SubcategoryRepository $subcategoryRepository,
        BreadcrumbsService $breadcrumbs,
    ): Response {
        $subcategory = $subcategoryRepository->findOneBy(['id' => $subcategoryId]);

        if (!$subcategory) {
            throw $this->createNotFoundException('Subcategory not found');
        }

        $breadcrumbs
            ->addHome()
            ->addSubcategory()
            ->add($subcategory->getName(), 'subcategory_read', ['subcategoryId' => $subcategoryId])
        ;

        return $this->render('subcategory/read.html.twig', [
            'controller_name' => 'SubcategoryController',
            'subcategory' => $subcategory,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/update/{subcategoryId<\d+>}', name: 'update')]
    public function update(
        int $subcategoryId,
        SubcategoryRepository $subcategoryRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        BreadcrumbsService $breadcrumbs,
    ): Response {
        $subcategory = $subcategoryRepository->findOneBy(['id' => $subcategoryId]);

        $this->denyAccessUnlessGranted(SubcategoryVoter::UPDATE, $subcategory);

        if (!$subcategory) {
            throw $this->createNotFoundException('Subcategory not found');
        }

        $form = $this->createForm(SubcategoryType::class, $subcategory);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $subcategory = $form->getData();

            $entityManager->persist($subcategory);
            $entityManager->flush();

            return $this->redirectToRoute('subcategory_read', ['subcategoryId' => $subcategory->getId()]);
        }

        $breadcrumbs
            ->addHome()
            ->addSubcategory()
            ->add($subcategory->getName(), 'subcategory_read', ['subcategoryId' => $subcategoryId])
            ->add('Update', 'subcategory_update', ['subcategoryId' => $subcategoryId])
        ;

        return $this->render('subcategory/update.html.twig', [
            'controller_name' => 'SubcategoryController',
            'form' => $form,
            'subcategory' => $subcategory,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }

    #[Route('/delete/{subcategoryId<\d+>}', name: 'delete')]
    public function delete(
        int $subcategoryId,
        SubcategoryRepository $subcategoryRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        BreadcrumbsService $breadcrumbs,
    ): Response {
        $subcategory = $subcategoryRepository->findOneBy(['id' => $subcategoryId]);

        $this->denyAccessUnlessGranted(SubcategoryVoter::DELETE, $subcategory);

        if (!$subcategory) {
            throw $this->createNotFoundException('Subcategory not found');
        }

        $form = $this->createForm(SubcategoryType::class, $subcategory);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->remove($subcategory);
            $entityManager->flush();

            return $this->redirectToRoute('subcategory_index');
        }

        $breadcrumbs
            ->addHome()
            ->addSubcategory()
            ->add($subcategory->getName(), 'subcategory_read', ['subcategoryId' => $subcategoryId])
            ->add('Delete', 'subcategory_delete', ['subcategoryId' => $subcategoryId])
        ;

        return $this->render('subcategory/delete.html.twig', [
            'controller_name' => 'SubcategoryController',
            'form' => $form,
            'subcategory' => $subcategory,
            'breadcrumbs' => $breadcrumbs->all(),
        ]);
    }
}
