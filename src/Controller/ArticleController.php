<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


final class ArticleController extends AbstractController
{
    #[Route('/article', name: 'create_article')]
    public function index(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
    ): Response {
        // $author = new Author();
        // $author->setName('author' . rand(1, 1000));
        // $author->setEmail('author' . rand(1, 1000) . '@example.com');

        $article = new Article();
        $article->setTitle('title' . rand(1, 1000));
        $article->setContent('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.');
        $article->setPublishedAt(new \DateTime());

        // associate the article with an author (ID generated randomly as the default value)
        $repository = $entityManager->getRepository((Author::class));
        $author = $repository->findAll();
        $randomIndex = rand(1, 10);
        $random_author = $author[$randomIndex] ?? $author[0];
        $article->setAuthor($random_author);

        //validations
        $errors = $validator->validate($article);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new Response($errorsString);
        }

        // $entityManager->persist($author);
        $entityManager->persist($article);
        $entityManager->flush();

        return new Response(
            'Saved new article with id: ' . $article->getId() . ', written by ' . $random_author->getName()
        );
    }
}
