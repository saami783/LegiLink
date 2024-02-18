<?php

namespace App\Controller\Global;

use App\Entity\MessageContact;
use App\Enum\MessageStateEnum;
use App\Form\MessageContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {
        $message = new MessageContact();

        $form = $this->createForm(MessageContactType::class, $message);

        $form->handleRequest($request);

        /** Traitement des données du formulaire. */
        if ($form->isSubmitted() && $form->isValid())
        {
            $message->setSentAt(new \DateTimeImmutable());
            $message->setState(MessageStateEnum::NON_LU);
            $this->manager->persist($message);
            $this->manager->flush();

            $this->addFlash("success", "Votre message a bien été envoyé. Nous vous répondrons dans les
        plus brefs délais.");
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('global/contact/index.html.twig', ['form' => $form->createView()]);
    }
}
