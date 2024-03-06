<?php

namespace App\Repository;

use App\Entity\NotificationUser;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotificationUser>
 *
 * @method NotificationUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationUser[]    findAll()
 * @method NotificationUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationUser::class);
    }

    /**
     * Count new notifications for a given user.
     *
     * @param User $user The user for whom to count new notifications.
     * @return int The number of new notifications.
     */
    public function countNewNotifications(User $user): int
    {
        $qb = $this->createQueryBuilder('nu');

        $qb->select('COUNT(nu.id)')
        ->innerJoin('nu.user', 'u')
        ->innerJoin('nu.notification', 'n')
        ->where('nu.isRead = :isRead')
        ->andWhere('u.id = :userId')
        ->setParameter('isRead', false)
        ->setParameter('userId', $user->getId());

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findNotificationsByUser(User $user)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT n FROM App\Entity\Notification n
            INNER JOIN App\Entity\NotificationUser nu WITH nu.notification = n
            WHERE nu.user = :user";

        $query = $entityManager->createQuery($dql)
            ->setParameter('user', $user);

        return $query->getResult();
    }


}
