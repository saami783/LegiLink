<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NotificationController extends AbstractController
{
    #[Route('/user/notification', name: 'app_user_notification')]
    public function index(): Response
    {
        return $this->render('user/notification/index.html.twig', [
            'controller_name' => 'NotificationController',
        ]);
    }
}
