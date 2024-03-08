<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\User\InfoUserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{

    public function __construct(private UserRepository $userRepository) { }

    #[Route('/profil', name: 'app_profil')]
    public function index(Request $request, UserRepository $userRepository): Response
    {

        /** @var User $user */
        $user = $this->getUser();
        $infoForm = $this->createForm(InfoUserType::class, $user);
        $infoForm->handleRequest($request);

        $passwordForm = $this->createForm(ChangePasswordFormType::class, $user);
        $passwordForm->handleRequest($request);

        if ($infoForm->isSubmitted()) {
            if($infoForm->isValid()) {
                $this->userRepository->save($user, true);
            } else{
                $this->addFlash('error', 'Le formulaire d\'informations n\'est pas valide.');
                $this->redirectToRoute('app_profil');
            }
        }
        if ($passwordForm->isSubmitted()) {
            if($passwordForm->isValid()) {
                $newPlainTextPassword = $passwordForm->get('plainPassword')->getData();
                $this->userRepository->upgradePassword($user, $newPlainTextPassword);
            } else {
                $this->addFlash('error', 'Le formulaire de modification de mot de passe n\'est pas valide.');
                $this->redirectToRoute('app_profil');
            }
        }

        return $this->render('user/profil/index.html.twig', [
            'infoForm' => $infoForm->createView(),
            'passwordForm' => $passwordForm->createView(),
        ]);
    }

    #[Route('/delete', name: 'app_profil_delete')]
    public function delete() : Response {

        /** @var User $user */
        $user = $this->getUser();
        $this->userRepository->remove($user, true);

        return $this->redirectToRoute('app_home');
    }

}
