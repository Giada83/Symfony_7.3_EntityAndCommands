<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(AuthorRepository $authorRepository, ArticleRepository $articleRepository): Response
    {
        //dd($authorRepository->countArticlesPerAuthor());
        // $data = $articleRepository->groupArticlesFromPublishedData();
        // dd($data);

        return $this->render('base.html.twig');
    }
}
