<?php

namespace App\Controller\Public\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    public function __construct(private AuthorizationCheckerInterface $authorizationChecker, private UrlGeneratorInterface $urlGenerator) {

    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             if ($this->authorizationChecker->isGranted('ROLE_USER')) {
                 return new RedirectResponse($this->urlGenerator->generate('app_user_dashboard'));
             } else if ($this->authorizationChecker->isGranted('ROLE_ADMIN')
                 || $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
                 return new RedirectResponse($this->urlGenerator->generate('admin'));
             }
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('views/public/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
