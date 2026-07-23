<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\CategoryRepository;
use App\Repository\ClimbRepository;
use App\Repository\SubcategoryRepository;
use AutoMapper\AutoMapperInterface;

final class ClimbRankedProvider implements ProviderInterface
{
    public function __construct(
        private ClimbRepository $climbRepository,
        private CategoryRepository $categoryRepository,
        private SubcategoryRepository $subcategoryRepository,
        private AutoMapperInterface $autoMapper,
    ) {
    }

    public function provide(
        Operation $operation,
        array $uriVariables = [],
        array $context = [],
    ): iterable {
        $category = $this->categoryRepository->find(
            (int) $uriVariables['categoryId'],
        );
        $subcategory = $this->subcategoryRepository->find(
            (int) $uriVariables['subcategoryId'],
        );
        $amount = (int) $uriVariables['amount'];

        $climbs = $this->climbRepository->findByCategoryAndSubcategoryAndRankSortByRank(
            $category,
            $subcategory,
            $amount,
        );

        return array_map(
            fn ($climb) => $this->autoMapper->map(
                $climb,
                $operation->getClass()
            ),
            iterator_to_array($climbs),
        );
    }
}
