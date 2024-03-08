<?php

namespace App\Repository;

use App\Entity\ApiExecution;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countExecutionsForPeriod(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, User $user): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.executedAt BETWEEN :startDate AND :endDate')
            ->andWhere('e.user = :user')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countRequestsForPeriod(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, User $user): int
    {
        $result = $this->createQueryBuilder('e')
            ->select('SUM(e.request) as totalRequests')
            ->where('e.executedAt BETWEEN :startDate AND :endDate')
            ->andWhere('e.user = :user')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return $result !== null ? (int) $result : 0;
    }

    // Cette méthode suppose que vous avez une colonne ou une manière de récupérer la limite quotidienne de l'utilisateur

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function calculateRemainingRequests(User $user, int $dailyRequestLimit): int
    {
        $totalRequestsToday = $this->countRequestsForPeriod(new \DateTimeImmutable('today'), new \DateTimeImmutable('now'), $user);

        return max($dailyRequestLimit - $totalRequestsToday, 0);
    }

}
