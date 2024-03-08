<?php

namespace App\Controller\User;

use App\Entity\Document;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\User\InfoUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class ProfilController extends AbstractController
{

    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $entityManager) { }

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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/delete', name: 'app_profil_delete')]
    public function delete( SessionInterface $session,
                            TokenStorageInterface $tokenStorage) : Response {

        /** @var User $user */
        $user = $this->getUser();

        $currentLatestDocument = $this->entityManager->getRepository(Document::class)->findOneBy([
            'user' => $user,
            'isLastest' => true,
        ]);

        if($currentLatestDocument) {
            $projectDir = $this->getParameter('kernel.project_dir');

            $filePath = $projectDir . '/public/uploads/files/' . $currentLatestDocument->getFileName();

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $this->container->get('security.token_storage')->setToken(null);

        $this->userRepository->remove($user, true);

        return $this->redirectToRoute('app_home');
    }


}
