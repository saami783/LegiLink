<?php

namespace App\Controller\User;

use App\Service\FileDownloadHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MediaController extends AbstractController
{
    #[Route('/media', name: 'app_user_media')]
    public function index(): Response
    {

        return $this->render('user/media/index.html.twig', [
            'controller_name' => 'MediaController',
        ]);
    }

    #[Route('/download', name: 'app_user_download')]
    public function download(FileDownloadHandler $downloadHandler): Response
    {
        $user = $this->getUser();

        return $downloadHandler->getDownloadResponse($user);
    }
}
