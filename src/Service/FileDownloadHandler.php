<?php

namespace App\Service;

use App\Entity\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FileDownloadHandler
{
    private $entityManager;
    private $projectDir;

    public function __construct(EntityManagerInterface $entityManager, string $projectDir)
    {
        $this->entityManager = $entityManager;
        $this->projectDir = $projectDir;
    }

    public function getDownloadResponse($user): BinaryFileResponse
    {
        $document = $this->entityManager->getRepository(Document::class)->findOneBy([
            'user' => $user,
            'isLastest' => true,
        ]);

        if (!$document) {
            throw new NotFoundHttpException('Le fichier demandé n\'existe pas.');
        }

        $fileName = $document->getFileName();
        $filePath = $this->projectDir . '/public/uploads/files/' . $fileName;


        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('Le fichier demandé n\'existe pas.');
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }
}