<?php

namespace App\Controller\Global;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FaqController extends AbstractController
{
    #[Route('/faq', name: 'app_faq')]
    public function index(): Response
    {
        return $this->render('global/faq/index.html.twig', [
            'controller_name' => 'FaqController',
        ]);
    }
}
