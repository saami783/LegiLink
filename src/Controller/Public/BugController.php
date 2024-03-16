<?php

namespace App\Controller\Public;

use App\Entity\MessageContact;
use App\Form\MessageContactType;
use App\Service\MessageService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BugController extends AbstractController
{

    public function __construct(private MessageService $messageService) {}

    #[Route('/report/bug', name: 'app_bug')]
    public function index(Request $request): Response
    {
        $message = new MessageContact();
        $form = $this->createForm(MessageContactType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
           $isPersisted = $this->messageService->persistMessage($message, true);
           if($isPersisted) {
               $this->addFlash("success", "Votre signalement a bien été envoyé.");
               return $this->redirectToRoute('app_bug');
           }
        }
        return $this->render('views/public/bug/index.html.twig', ['form' => $form->createView()]);
    }
}
