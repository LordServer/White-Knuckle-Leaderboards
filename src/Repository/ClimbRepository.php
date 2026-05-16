<?php

namespace App\Repository;

use App\Entity\Climb;
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
            ->addSelect('sum(c.time) as totalTime')
            ->addSelect('sum(c.height) as totalHeight')
            ->addSelect('count(case when c.created_at >= :pastDate then c.id else 0 end) as recentClimbs')
            ->setParameters($parameters)
            ->getQuery()
            ->getSingleResult()
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
