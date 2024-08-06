<?php

namespace App\Controller\Public;

use App\Entity\MessageContact;
use App\Form\MessageContactType;
use App\Service\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{

    public function __construct(private MessageService $messageService) { }

    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {
        $message = new MessageContact();
        $form = $this->createForm(MessageContactType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $isPersisted = $this->messageService->persistMessage($message, false);
            if($isPersisted) {
                $this->addFlash("success", "Votre message a bien été envoyé. Nous vous répondrons dans les
        plus brefs délais.");
                return $this->redirectToRoute('app_contact');
            }
        }
        return $this->render('views/public/contact/index.html.twig', ['form' => $form->createView()]);
    }
}
