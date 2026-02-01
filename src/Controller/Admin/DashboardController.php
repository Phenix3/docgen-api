<?php

namespace App\Controller\Admin;

use App\Entity\Country;
use App\Entity\Document;
use App\Entity\DocumentType;
use App\Entity\Template;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('@EasyAdmin/page/content.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('DocGen API Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        
        yield MenuItem::section('Contenu');
        yield MenuItem::linkToCrud('Templates', 'fas fa-file-code', Template::class);
        yield MenuItem::linkToCrud('Types de Documents', 'fas fa-tags', DocumentType::class);
        
        yield MenuItem::section('Suivi');
        yield MenuItem::linkToCrud('Documents Générés', 'fas fa-file-pdf', Document::class);

        yield MenuItem::section('Configuration');
        yield MenuItem::linkToCrud('Pays', 'fas fa-globe', Country::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
    }
}
