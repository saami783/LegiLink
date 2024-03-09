<?php

namespace App\Controller\Global;

use App\Repository\FaqRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FaqController extends AbstractController
{
    public function __construct(private FaqRepository $faqRepository) { }

    #[Route('/faq', name: 'app_faq')]
    public function index(): Response
    {

        return $this->render('global/faq/index.html.twig', [
            'questions' => $this->faqRepository->findBy(['isVisible' => true]),
        ]);
    }
}
