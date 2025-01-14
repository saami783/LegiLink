<?php

namespace App\Controller\User;

use App\Entity\Api;
use App\Form\ApiFormType;
use App\Security\Voter\ApiVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/dashboard/config/api', name: 'app_user_api')]
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

                    if(!is_null($oldApi)) {
                        $oldApi->setIsDefault(false);
                        $this->entityManager->persist($oldApi);
                        $this->entityManager->flush();
                    }

                }

                $newApi->setUser($user);
                $this->entityManager->persist($newApi);
                $this->entityManager->flush();

                $this->addFlash('success', 'La nouvelle configuration API a été ajoutée avec succès.');

                $this->entityManager->close();

                return $this->redirectToRoute('app_user_api');
            }
        }
        return $this->render('views/user/api/index.html.twig', [
            'apis' => $apis,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dashboard/api/update/{id}', name: 'app_user_api_update')]
    public function update(Api $api) : Response {
        $this->denyAccessUnlessGranted(ApiVoter::EDIT, $api);

       $defaultApi = $this->entityManager->getRepository(Api::class)->findOneBy(['isDefault' => true]);

       if (!is_null($defaultApi)) {
           $defaultApi->setIsDefault(false);
       }
       $api->setIsDefault(true);
       $this->entityManager->persist($api);
       $this->entityManager->flush();

        return $this->redirectToRoute('app_user_api');
    }

    #[Route('/dashboard/api/delete/{id}', name: 'app_user_api_delete')]
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
