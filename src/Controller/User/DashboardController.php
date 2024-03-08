<?php

namespace App\Controller\User;

use App\Entity\Api;
use App\Entity\Document;
use App\Entity\User;
use App\Form\FileType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UpdateFileService;

class DashboardController extends AbstractController
{

    public function __construct(private UpdateFileService $fileService,
                                private EntityManagerInterface $entityManager) { }


    /**
     * @throws Exception
     */
    #[Route('/dashboard', name: 'app_user_dashboard')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $document = new Document();
        $form = $this->createForm(FileType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($this->formTreatment($user, $entityManager, $document)) {
                return $this->redirectToRoute('app_user_media');
            } else {
                return $this->redirectToRoute('app_user_dashboard');
            }
        }

            return $this->render('user/dashboard/index.html.twig', [
                'form' => $form->createView(),
            ]);
        }

    /**
     * @throws Exception
     */
    private function formTreatment(User $user, EntityManagerInterface $entityManager, Document $document) : bool
        {

            /** @var Api $api */
            $api = $this->entityManager->getRepository(Api::class)->findOneBy([
                'user' => $user,
                'isDefault' => true
            ]);

            if (is_null($api)) {
                $this->addFlash('error', 'Vous devez définir une clé API par défaut pour upload un fichier.');
                return false;
            }

            $currentLatestDocument = $entityManager->getRepository(Document::class)->findOneBy([
                'user' => $user,
                'isLastest' => true,
            ]);

            if ($currentLatestDocument) {

                $projectDir = $this->getParameter('kernel.project_dir');

                $filePath = $projectDir . '/public/uploads/files/' . $currentLatestDocument->getFileName();

                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                $currentLatestDocument->setIsLastest(false);
                $entityManager->persist($currentLatestDocument);
            }

            $document->setIsLastest(true);
            $document->setUser($user);
            $entityManager->persist($document);
            $entityManager->flush();

            $this->addFlash('success', 'Fichier uploadé avec succès.');

            try {
                $this->fileService->updateFile($api, $user);
                return true;
            } catch (Exception $e) {
                return false;
            }

        }


}
