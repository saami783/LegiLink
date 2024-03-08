<?php

namespace App\Repository;

use App\Entity\ApiExecution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiExecution>
 *
 * @method ApiExecution|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiExecution|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiExecution[]    findAll()
 * @method ApiExecution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiExecutionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiExecution::class);
    }

    //    /**
    //     * @return ApiExecution[] Returns an array of ApiExecution objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ApiExecution
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
