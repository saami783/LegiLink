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
    #[Route('/api', name: 'app_user_api')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $apis = $entityManager->getRepository(Api::class)->findBy(['user' => $user]);

        $newApi = new Api();
        $form = $this->createForm(ApiFormType::class, $newApi);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (count($apis) >= 3) {
                $this->addFlash('error', 'Vous ne pouvez pas ajouter plus de 3 configurations API.');
            } else if ($form->isValid()) {
                $this->denyAccessUnlessGranted(ApiVoter::NEW, $newApi);

                $newApi->setUser($user);
                $entityManager->persist($newApi);
                $entityManager->flush();

                $this->addFlash('success', 'La nouvelle configuration API a été ajoutée avec succès.');

                return $this->redirectToRoute('app_user_api');
            }
        }
        return $this->render('user/api/index.html.twig', [
            'apis' => $apis,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/delete/{id}', name: 'app_user_api_delete')]
    public function delete(Api $api, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(ApiVoter::DELETE, $api);

        $entityManager->remove($api);
        $entityManager->flush();

        $this->addFlash('success', 'La configuration API a été supprimée avec succès.');

        return $this->redirectToRoute('app_user_api');
    }
}
