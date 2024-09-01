<?php

namespace App\Controller\User;

use App\Entity\Document;
use App\Entity\User;
use App\Repository\ApiRepository;
use App\Repository\DocumentRepository;
use App\Repository\SettingRepository;
use App\Security\Voter\DocumentVoter;
use App\Service\FileDownloadHandlerService;
use App\Service\FileStrategyManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class MediaController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly FileStrategyManagerService $strategyManagerService)
    {

    }

    #[Route('/dashboard/media', name: 'app_user_media')]
    public function index(): Response
    {
        $document = $this->entityManager->getRepository(Document::class)->findOneBy([
            'user' => $this->getUser(),
            'isLastest' => true,
        ]);

        $this->entityManager->close();

        return $this->render('views/user/media/index.html.twig', [
            'documentId' => $document ? $document->getId() : null,
        ]);
    }

    #[Route('/dashboard/media/download/{id}', name: 'app_user_download')]
    public function download(FileDownloadHandlerService $downloadHandler, Document $document): Response
    {

        $this->denyAccessUnlessGranted(DocumentVoter::DOWNLOAD, $document);

        try {
            return $downloadHandler->getDownloadResponse($document);
        } catch (NotFoundHttpException $e) {
            $this->addFlash('error', 'Le fichier demandé n\'existe pas.');
            return $this->redirectToRoute('app_user_media');
        }
    }

    #[Route('/dashboard/media/update', name: 'app_user_media_update')]
    public function update(ApiRepository $apiRepository,
                           DocumentRepository $documentRepository,
                           SettingRepository $settingRepository) : Response
    {

        /** @var User $user */
        $user = $this->getUser();

        $this->strategyManagerService->executeStrategy();

        // $this->addFlash('success', 'Votre fichier est prêt à être télécharger ! ');
        return new Response('Coucou', 200);
    }

}
