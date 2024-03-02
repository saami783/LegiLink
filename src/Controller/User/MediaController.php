<?php

namespace App\Controller\User;

use App\Entity\Document;
use App\Security\Voter\DocumentVoter;
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

    public function __construct(private EntityManagerInterface $entityManager) {

    }
    #[Route('/media', name: 'app_user_media')]
    public function index(): Response
    {
        $document = $this->entityManager->getRepository(Document::class)->findOneBy([
            'user' => $this->getUser(),
            'isLastest' => true,
        ]);

        $this->entityManager->close();

        return $this->render('user/media/index.html.twig', [
            'documentId' => $document ? $document->getId() : null,
        ]);
    }

    #[Route('/download/{id}', name: 'app_user_download')]
    public function download(FileDownloadHandler $downloadHandler, Document $document): Response
    {

        $this->denyAccessUnlessGranted(DocumentVoter::DOWNLOAD, $document);

        try {
            return $downloadHandler->getDownloadResponse($document);
        } catch (NotFoundHttpException $e) {
            $this->addFlash('error', 'Le fichier demandé n\'existe pas.');
            return $this->redirectToRoute('app_user_media');
        }

    }
}
