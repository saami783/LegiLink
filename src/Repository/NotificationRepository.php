<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 *
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * Count new notifications for a given user.
     *
     * @param User $user The user for whom to count new notifications.
     * @return int The number of new notifications.
     */
    public function countNewNotifications(User $user): int
    {
        return $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->join('n.users', 'u')
            ->where('u.id = :userId')
            ->andWhere('n.isNew = :isNew')
            ->setParameter('userId', $user->getId())
            ->setParameter('isNew', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

}
