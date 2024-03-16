<?php

namespace App\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DocumentationController extends AbstractController
{
    #[Route('/documentation/videos', name: 'app_documentation_video')]
    public function index(): Response
    {
        return $this->render('views/public/documentation/video.html.twig');
    }

    #[Route('/documentation', name: 'app_documentation')]
    public function detail(): Response
    {
        return $this->render('views/public/documentation/index.html.twig');
    }

}
