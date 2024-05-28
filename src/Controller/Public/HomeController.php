<?php

namespace App\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Repository\UserRepository;

class HomeController extends AbstractController
{

    public function __construct(private UserRepository $userRepository) {}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('/public/home/index.html.twig', [
            'teacher' => $this->userRepository->count(['profession' => 'teacher']),
            'student' => $this->userRepository->count(['profession' => 'student']),
            'registered' => $this->userRepository->count([])
        ]);
    }
}
