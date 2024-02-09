<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MediaController extends AbstractController
{
    #[Route('/user/media', name: 'app_user_media')]
    public function index(): Response
    {
        return $this->render('user/media/index.html.twig', [
            'controller_name' => 'MediaController',
        ]);
    }
}
