<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Subcategory;
use App\Repository\ClimbRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdateClimbRanks
{
    public function __construct(
        private readonly ClimbRepository $climbRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function updateClimbRanks(Category $category, Subcategory $subcategory): void
    {
        $rows = $this->climbRepository->findForRanking($category, $subcategory);

        $bestPerClimber = [];

        foreach ($rows as $row) {
            $userId = $row->getClimber()->getId();

            if (!isset($bestPerClimber[$userId])) {
                $bestPerClimber[$userId] = $row;
            }
        }

        foreach ($rows as $row) {
            $row->setRank(null);
        }

        $rank = 1;

        $previousScore = null;
        $rank = 0;
        $displayRank = 0;

        foreach ($bestPerClimber as $row) {
            ++$rank;

            if ($row->getScore() !== $previousScore) {
                $displayRank = $rank;
            }

            $row->setRank($displayRank);

            $previousScore = $row->getScore();
        }

        $this->entityManager->flush();
    }
}
