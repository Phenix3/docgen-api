<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SpaController extends AbstractController
{
    #[Route('/', name: 'spa_index')]
    #[Route(
        '/{reactRouting}',
        name: 'spa_catch_all',
        requirements: ['reactRouting' => '^(?!api|_).*'],
        defaults: ['reactRouting' => null],
        priority: -1
    )]
    public function index(): Response
    {
        return $this->render('spa.html.twig');
    }
}
