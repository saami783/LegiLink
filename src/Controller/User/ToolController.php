<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ToolController extends AbstractController
{
    #[Route('/tools', name: 'app_user_tool')]
    public function index(): Response
    {
        return $this->render('user/tools/index.html.twig', [
            'controller_name' => 'ToolController',
        ]);
    }
}
