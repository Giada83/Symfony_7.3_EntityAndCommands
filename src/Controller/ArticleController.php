<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Author;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleController extends AbstractController
{
    #[Route('/article', name: 'create_article')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $author = new Author();
        $author->setName('author' . rand(1, 1000));
        $author->setEmail('author' . rand(1, 1000) . '@example.com');

        $article = new Article();
        $article->setTitle('title' . rand(1, 1000));
        $article->setContent('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.');
        $article->setPublishedAt(new \DateTime());

        // relates this article to the author
        $article->setAuthor($author);

        $entityManager->persist($author);
        $entityManager->persist($article);
        $entityManager->flush();

        return new Response(
            'Saved new article with id: ' . $article->getId()
                . ' and new author with id: ' . $author->getId()
        );
    }
}
