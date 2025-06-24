<?php

namespace App\Controller\Api;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/authors', name: 'api_authors_')] #rotta globale
final class AuthorApiController extends AbstractController
{
    # GET /list | Index
    #[Route('/', name: 'api_authors_index', methods: ['GET'])]
    public function index(AuthorRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $authors = $repo->findAll();
        $data = $serializer->serialize($authors, 'json', ['groups' => 'author:fields']);
        return new JsonResponse($data, 200, [], true);
    }

    // GET /{id} | Show
    #[Route('/{id}', name: 'api_authors_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Author $author, SerializerInterface $serializer): JsonResponse //Author $author = param Converter automatico | $author = $authorRepository->find($id);
    {
        $json = $serializer->serialize($author, 'json', ['groups' => 'author:fields']);
        return new JsonResponse($json, 200, [], true);
    }

    // POST | Create
    #[Route('', name: 'api_authors_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $author = $serializer->deserialize(
            $request->getContent(),  // 1. Recupera il corpo (body) della richiesta HTTP da Json in formato stringa
            Author::class,          // 2. La classe PHP in cui vogliamo convertire i dati JSON
            'json',
            [
                'groups' => 'author:write'
            ]
        );

        $errors = $validator->validate($author);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return $this->json($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($author);
        $em->flush();

        $data = $serializer->serialize($author, 'json', ['groups' => 'author:fields']);

        return new JsonResponse($data, 200, [], true);
    }

    // PUT | Update
    #[Route('/{id}', name: 'api_authors_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(Author $author, Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $serializer->deserialize($request->getContent(), Author::class, 'json', [
            'groups' => 'author:write',
            'object_to_populate' => $author //opzione del metodo deserialize(), che Symfony usa internamente per capire su quale oggetto esistente deve scrivere i dati JSON.
        ]);

        $errors = $validator->validate($author);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return $this->json($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $em->flush();

        $data = $serializer->serialize($author, 'json', ['groups' => 'author:fields']);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    // DELETE
    #[Route('/{id}', name: 'api_authors_delete', methods: ['DELETE'])]
    public function delete(Author $author, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($author);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT); //204
    }
}
