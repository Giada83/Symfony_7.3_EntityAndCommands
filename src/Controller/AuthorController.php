<?php

namespace App\Controller;

use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthorController extends AbstractController
{
    #[Route('/author', name: 'create_author')]
    public function createAuthor(EntityManagerInterface $entityManager): Response
    {
        $author = new Author();
        $author->setName('admin');
        $author->setEmail('admin@admin.it');

        // tell Doctrine you want to (eventually) save the author (no queries yet)
        $entityManager->persist($author);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new author with id ' . $author->getId());
    }
}
