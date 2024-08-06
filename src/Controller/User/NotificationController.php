<?php

namespace App\Controller\User;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Repository\NotificationUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function index(Request $request, NotificationUserRepository $notificationUserRepository, PaginatorInterface $paginator): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $query = $this->notificationUserRepository->findNotificationsForUser($user);

        $notificationsPaginated = $paginator->paginate(
            $query, // la requête, pas le résultat
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('views/user/notifications/index.html.twig', [
            'pagination' => $notificationsPaginated
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

        return $this->render('views/user/notifications/detail.html.twig', [
            'notification' => $notification
        ]);
    }


}
