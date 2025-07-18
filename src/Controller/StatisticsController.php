<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StatisticsController extends AbstractController
{
    #[Route('/statistics', name: 'statistics_page')]
    public function index(): Response
    {
        return $this->render('statistics/index.html.twig');
    }
}
