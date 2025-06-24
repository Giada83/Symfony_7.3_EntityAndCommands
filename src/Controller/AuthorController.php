<?php

namespace App\Controller;

use App\Entity\Author; //import entity
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface; //import Validator
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthorController extends AbstractController
{
    #[Route('/author', name: 'create_author')]
    #Persistent record on db
    public function createAuthor(
        EntityManagerInterface $entityManager, //saving and retrieving objects from the database
        ValidatorInterface $validator
    ): Response {
        $author = new Author();
        $author->setName('author' . rand(1, 100));
        $author->setEmail('author' . rand(1, 100) . '@example.com');

        //validations
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

    #[Route('/author/list', name: 'author_list')]
    // #index method via repository (access to methods and functionalities for data management)
    public function index(AuthorRepository $authorRepository): Response
    {
        // dd($authorRepository->findAll());
        $authors = $authorRepository->findAll();

        return $this->render('author/index.html.twig', [
            'authors' => $authors,
        ]);
    }
}
