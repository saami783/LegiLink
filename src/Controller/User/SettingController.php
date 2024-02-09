<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SettingController extends AbstractController
{
    #[Route('/user/config', name: 'app_user_setting')]
    public function index(): Response
    {
        return $this->render('user/config/index.html.twig', [
            'controller_name' => 'SettingController',
        ]);
    }
}
