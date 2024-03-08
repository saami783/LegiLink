<?php

namespace App\Controller\User;

use App\Entity\Api;
use App\Entity\Document;
use App\Entity\Setting;
use App\Entity\User;
use App\Form\FileType;
use App\Service\StatisticsService;
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
                                private EntityManagerInterface $entityManager,
                                private StatisticsService $statisticsService) { }


    /**
     * @throws Exception
     */
    #[Route('/dashboard', name: 'app_user_dashboard')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Setting $setting */
        $setting = $this->entityManager->getRepository(Setting::class)->findOneBy(['user' => $user]);

        $document = new Document();
        $form = $this->createForm(FileType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($this->formTreatment($user, $entityManager, $document, $setting)) {
                return $this->redirectToRoute('app_user_media');
            } else {
                return $this->redirectToRoute('app_user_dashboard');
            }
        }

        if($setting->getDailyRequestLimit() - $setting->getTotalRequestSent() >= 1
            && $setting->getDailyRequestLimit() - $setting->getTotalRequestSent() <= 20
            && !$setting->isIsAutoBlockRequests()) {
            $this->addFlash('warning', "Vous êtes sur le point d'atteindre votre limite quotidienne de requêtes. Pour éviter tout dépassement, envisagez d'activer le blocage automatique des requêtes 🚫");
        }

        $statistics = $this->statisticsService->getStatistics($user, $setting->getDailyRequestLimit());

        return $this->render('user/dashboard/index.html.twig', [
            'form' => $form->createView(),
            'statistics' => $statistics
            ]);
        }

    /**
     * @throws Exception
     */
    private function formTreatment(User $user, EntityManagerInterface $entityManager, Document $document, Setting $setting) : bool
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

            if($setting->getDailyRequestLimit() - $setting->getTotalRequestSent() <= 0 && $setting->isIsAutoBlockRequests()) {
                $this->addFlash('error', 'Vous avez atteint la limite de requêtes quotidienne.');
                return false;
            }
            $this->addFlash('success', 'Votre fichier est prêt à être télécharger ! ');

            try {
                $this->fileService->updateFile($api, $user, $setting);
                return true;
            } catch (Exception $e) {
                return false;
            }

        }


}
