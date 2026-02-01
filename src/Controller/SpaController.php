<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SpaController extends AbstractController
{
    /**
     * Catch-all route for the SPA
     * This must be loaded LAST to not override API routes
     */
    #[Route(
        '/{reactRouting}',
        name: 'spa',
        requirements: ['reactRouting' => '^(?!api|_).*'],
        defaults: ['reactRouting' => null],
        priority: -1
    )]
    public function index(): Response
    {
        return $this->render('spa.html.twig');
    }
}
