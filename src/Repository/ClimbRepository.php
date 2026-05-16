<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Climb;
use App\Entity\Subcategory;
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
            ->addSelect('count(case when c.created_at >= :pastDate then c.id else 0 end) as recentClimbs')
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
            case 'Time':
                $qb->orderBy('c.time', 'ASC');
                break;
            case 'Height':
                $qb->orderBy('c.height', 'DESC');
                break;
            case 'Speed':
                $qb->orderBy('c.speed', 'DESC');
                break;
        }

        return $qb->setParameters($parameters)->getQuery()->getResult();
    }

    public function findByCategoryAndSubcategorySortByCreateAt(Category $category, Subcategory $subcategory)
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
