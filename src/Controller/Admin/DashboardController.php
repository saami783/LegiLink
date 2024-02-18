<?php

namespace App\Controller\Admin;

use App\Entity\Document;
use App\Entity\MessageContact;
use App\Entity\Notification;
use App\Entity\Setting;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
         return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('LegiLink Admin Dashboard');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Messages', 'fa-solid fa-comment', MessageContact::class);
        yield MenuItem::linkToCrud('Notifications', 'fa-solid fa-bell', Notification::class);
        yield MenuItem::linkToCrud('Document Utilisateur', 'fa-solid fa-file', Document::class);
        yield MenuItem::linkToCrud('Paramètre Utilisateur', 'fa-solid fa-gears', Setting::class);
    }
}
