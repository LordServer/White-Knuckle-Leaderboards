<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Climb;
use App\Entity\Subcategory;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Climb>
 */
class ClimbRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Climb::class);
    }

    public function getApprovals(): string
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->andWhere('c.is_reviewed = FALSE')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getRecentClimbs(int $limit = 5): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.created_at', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getRecentUserClimbs(): int
    {
        $oneMonthAgo = new \DateTime('-1 month');

        $parameters = new ArrayCollection([
            new Parameter('pastDate', $oneMonthAgo),
        ]);

        return $this->createQueryBuilder('c')
            ->select('COUNT(DISTINCT c.climber)')
            ->where('c.created_at >= :pastDate')
            ->setParameters($parameters)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getClimbStats(): array
    {
        $oneMonthAgo = new \DateTime('-1 month');

        $parameters = new ArrayCollection([
            new Parameter('pastDate', $oneMonthAgo),
        ]);

        return $this->createQueryBuilder('c')
            ->select('count(c.id) as totalClimbs')
            ->addSelect('coalesce(sum(c.time), 0) as totalTime')
            ->addSelect('coalesce(sum(c.height), 0) as totalHeight')
            ->addSelect('sum(case when c.created_at >= :pastDate then 1 else 0 end) as recentClimbs')
            ->setParameters($parameters)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    public function findForRanking(Category $category, Subcategory $subcategory): array
    {
        $parameters = new ArrayCollection([
            new Parameter('category', $category),
            new Parameter('subcategory', $subcategory),
            new Parameter('status', 'approved'),
        ]);

        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.category = :category')
            ->andWhere('c.subcategory = :subcategory')
            ->andWhere('c.status = :status');

        switch ($category->getRankMethod()->getName()) {
            case 'Score':
                $qb->orderBy('c.score', 'DESC');
                break;
            case 'Score Ascending':
                $qb->orderBy('c.score', 'ASC');
                break;
            case 'Time':
                $qb->orderBy('c.time', 'DESC');
                break;
            case 'Time Ascending':
                $qb->orderBy('c.time', 'ASC');
                break;
            case 'Height':
                $qb->orderBy('c.height', 'DESC');
                break;
            case 'Height Ascending':
                $qb->orderBy('c.height', 'ASC');
                break;
            case 'Speed':
                $qb->orderBy('c.speed', 'DESC');
                break;
            case 'Speed Ascending':
                $qb->orderBy('c.speed', 'ASC');
                break;
        }

        return $qb->setParameters($parameters)->getQuery()->getResult();
    }

    public function findByCategoryAndSubcategorySortByCreateAt(?Category $category, ?Subcategory $subcategory)
    {
        $parameters = new ArrayCollection([
            new Parameter('category', $category),
            new Parameter('subcategory', $subcategory),
        ]);

        return $this->createQueryBuilder('c')
            ->andWhere('c.category = :category')
            ->andWhere('c.subcategory = :subcategory')
            ->setParameters($parameters)
            ->orderBy('c.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCategoryAndSubcategoryAndRankSortByRank(?Category $category, ?Subcategory $subcategory)
    {
        $parameters = new ArrayCollection([
            new Parameter('category', $category),
            new Parameter('subcategory', $subcategory),
        ]);

        return $this->createQueryBuilder('c')
            ->andWhere('c.category = :category')
            ->andWhere('c.subcategory = :subcategory')
            ->andWhere('c.rank IS NOT NULL')
            ->setParameters($parameters)
            ->orderBy('c.rank', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCategoryAndSubcategoryAndApprovalStatusSortByOldestCreatedAt(?Category $category, ?Subcategory $subcategory, bool $approved)
    {
        $parameters = new ArrayCollection([
            new Parameter('category', $category),
            new Parameter('subcategory', $subcategory),
            new Parameter('status', $approved),
        ]);

        return $this->createQueryBuilder('c')
            ->andWhere('c.category = :category')
            ->andWhere('c.subcategory = :subcategory')
            ->andWhere('c.is_reviewed = :status')
            ->setParameters($parameters)
            ->orderBy('c.created_at', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByUserAndCategoryAndSubcategoryOrderByNewestCreatedAt(?User $climber, Category $category, ?Subcategory $subcategory)
    {
        $parameters = new ArrayCollection([
            new Parameter('climber', $climber),
            new Parameter('category', $category),
            new Parameter('subcategory', $subcategory),
        ]);

        return $this->createQueryBuilder('c')
            ->andWhere('c.climber = :climber')
            ->andWhere('c.category = :category')
            ->andWhere('c.subcategory = :subcategory')
            ->setParameters($parameters)
            ->orderBy('c.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getUnreviewedBreakdown(): array
    {
        $results = $this->createQueryBuilder('c')
            ->select([
                'cat.id AS categoryId',
                'cat.name AS categoryName',
                'sc.id AS subcategoryId',
                'sc.name AS subcategoryName',
                'COUNT(c.id) AS total',
            ])
            ->join('c.category', 'cat')
            ->join('c.subcategory', 'sc')
            ->where('c.is_reviewed = FALSE')
            ->groupBy('cat.id, sc.id')
            ->orderBy('cat.id', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $grouped = [];

        foreach ($results as $row) {
            $categoryId = $row['categoryId'];

            if (!isset($grouped[$categoryId])) {
                $grouped[$categoryId] = [
                    'id' => $row['categoryId'],
                    'category' => $row['categoryName'],
                    'total' => 0,
                    'subcategories' => [],
                ];
            }

            $grouped[$categoryId]['subcategories'][] = [
                'id' => $row['subcategoryId'],
                'name' => $row['subcategoryName'],
                'total' => (int) $row['total'],
            ];

            $grouped[$categoryId]['total'] += (int) $row['total'];
        }

        return $grouped;
    }

    //    /**
    //     * @return Climb[] Returns an array of Climb objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Climb
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
