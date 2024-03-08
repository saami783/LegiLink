<?php

namespace App\Controller\User;

use App\Entity\Document;
use App\Form\FileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UpdateFileService;

class DashboardController extends AbstractController
{

    public function __construct(private UpdateFileService $fileService) { }

    #[Route('/dashboard', name: 'app_user_dashboard')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $document = new Document();
        $form = $this->createForm(FileType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

            try{
                $this->fileService->updateFile();
            }catch (\Exception $e) {

            }

            return $this->redirectToRoute('app_user_media');
        }

        return $this->render('user/dashboard/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
