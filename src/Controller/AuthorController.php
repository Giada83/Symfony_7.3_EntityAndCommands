<?php

namespace App\Controller;

use App\Entity\Author; //importare l'entità
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface; //importare Validator
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthorController extends AbstractController
{
    #[Route('/author', name: 'create_author')]
    public function createAuthor(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): Response {
        $author = new Author();
        $author->setName('admin');
        $author->setEmail('admin@admin.it');

        //controllo validazioni
        $errors = $validator->validate($author);
        if (count($errors) > 0) {
            /*
            * Uses a __toString method on the $errors variable which is a
            * ConstraintViolationList object. This gives us a nice string
            * for debugging.
            */
            $errorsString = (string) $errors;

            return new Response($errorsString);
        }

        // tell Doctrine you want to (eventually) save the author (no queries yet)
        $entityManager->persist($author);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new author with id ' . $author->getId());
    }
}
