<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Repository\NotificationUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Notification;

class NotificationController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager,
                                private NotificationUserRepository $notificationUserRepository,
                                private NotificationRepository $notificationRepository,
                                private Security $security) {

    }

    #[Route('/notifications/count', name: 'notifications_count')]
    public function count(): Response
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $count = $this->notificationUserRepository->countNewNotifications($user);

        return new Response($count);
    }

    #[Route('/notifications', name: 'app_user_notification')]
    public function index(NotificationUserRepository $notificationUserRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $notificationUsers = $notificationUserRepository->findBy(['user' => $user]);

        $notificationsWithReadStatus = [];
        foreach ($notificationUsers as $notificationUser) {
            $notificationsWithReadStatus[] = [
                'notification' => $notificationUser->getNotification(),
                'isRead' => $notificationUser->isRead()
            ];
        }

        return $this->render('user/notifications/index.html.twig', [
            'notificationsWithReadStatus' => $notificationsWithReadStatus
        ]);
    }


    #[Route('/notifications/{notification}', name: 'app_user_detail_notification')]
    public function detail(Notification $notification, NotificationUserRepository $notificationUserRepository): Response {
        $this->denyAccessUnlessGranted('NOTIFICATION_VIEW', $notification);

        $notificationUser = $notificationUserRepository->findOneBy([
            'notification' => $notification,
            'user' => $this->getUser()
        ]);

        if ($notificationUser) {
            $notificationUser->setRead(true);
            $this->entityManager->persist($notificationUser);
            $this->entityManager->flush();
        }
         $this->entityManager->close();

        return $this->render('user/notifications/detail.html.twig', [
            'notification' => $notification
        ]);
    }


}
