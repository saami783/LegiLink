<?php

namespace App\Controller\Public;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    public function __construct(private UserRepository $userRepository) {}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('views/public/home/index.html.twig', [
            'teacher' => $this->userRepository->count(['profession' => 'teacher']),
            'student' => $this->userRepository->count(['profession' => 'student']),
            'registered' => $this->userRepository->count([])
        ]);
    }
}
