<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Notification;

class NotificationController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager,
                                private NotificationRepository $notificationRepository,
                                private Security $security) {

    }

    #[Route('/notifications/count', name: 'notifications_count')]
    public function count(): Response
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $count = $this->notificationRepository->countNewNotifications($user);

        return new Response($count);
    }

    #[Route('/notifications', name: 'app_user_notification')]
    public function index(): Response
    {
        $user = $this->getUser();
        $notifications = $this->entityManager->getRepository(Notification::class)->findBy(['user' => $user]);

        $this->entityManager->close();

        return $this->render('user/notifications/index.html.twig', [
          'notifications' => $notifications
        ]);
    }

    #[Route('/notifications/{user}/{notification}', name: 'app_user_detail_notification')]
    public function detail(User $user, Notification $notification) : Response {

        $this->denyAccessUnlessGranted('NOTIFICATION_VIEW', $notification);

        $notification->setIsNew(false);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
        $this->entityManager->close();

        return $this->render('user/notifications/detail.html.twig',
        ['notifaction' => $notification]);
    }

}
