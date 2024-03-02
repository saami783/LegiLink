<?php

namespace App\Controller\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Notification;

class NotificationController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager) { }

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
}
