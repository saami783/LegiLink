<?php

namespace App\Controller\User;

use App\Entity\Setting;
use App\Entity\User;
use App\Form\SettingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/dashboard/config', name: 'app_user_setting')]
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $setting = $this->entityManager->getRepository(Setting::class)->findOneBy(['user' => $user]);

        if (!$setting) {
            $setting = new Setting();
            $setting->setUser($user);
        }

        $this->denyAccessUnlessGranted('SETTING_VIEW', $setting);

        $form = $this->createForm(SettingType::class, $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('SETTING_EDIT', $setting);

            $this->entityManager->persist($setting);
            $this->entityManager->flush();
            $this->entityManager->close();

            $this->addFlash('success', 'Paramètres sauvegardés avec succès.');

            return $this->redirectToRoute('app_user_setting');
        }

        return $this->render('views/user/config/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

