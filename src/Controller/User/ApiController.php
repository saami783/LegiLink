<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Api;
use App\Form\ApiFormType;
use App\Security\Voter\ApiVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/config/api', name: 'app_user_api')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $apis = $this->entityManager->getRepository(Api::class)->findBy(['user' => $user]);

        $newApi = new Api();
        $form = $this->createForm(ApiFormType::class, $newApi);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (count($apis) >= 3) {
                $this->addFlash('error', 'Vous ne pouvez pas ajouter plus de 3 configurations API.');
            } else if ($form->isValid()) {
                $this->denyAccessUnlessGranted(ApiVoter::NEW, $newApi);

                if($newApi->isIsDefault()) {
                    /** @var Api $oldApi */
                    $oldApi = $this->entityManager->getRepository(Api::class)->findOneBy(['isDefault' => true]);

                    $oldApi->setIsDefault(false);
                    $this->entityManager->persist($oldApi);
                    $this->entityManager->flush();
                }

                $newApi->setUser($user);
                $this->entityManager->persist($newApi);
                $this->entityManager->flush();

                $this->addFlash('success', 'La nouvelle configuration API a été ajoutée avec succès.');

                $this->entityManager->close();

                return $this->redirectToRoute('app_user_api');
            }
        }
        return $this->render('user/api/index.html.twig', [
            'apis' => $apis,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/delete/{id}', name: 'app_user_api_delete')]
    public function delete(Api $api): Response
    {
        $this->denyAccessUnlessGranted(ApiVoter::DELETE, $api);

        $this->entityManager->remove($api);
        $this->entityManager->flush();
        $this->entityManager->close();

        $this->addFlash('success', 'La configuration API a été supprimée avec succès.');

        return $this->redirectToRoute('app_user_api');
    }
}
