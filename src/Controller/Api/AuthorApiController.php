<?php

namespace App\Controller\Api;

use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/authors', name: 'api_authors_')]
final class AuthorApiController extends AbstractController
{
    public function __construct( //autowire | iniziezione automatica dei servizi
        protected EntityManagerInterface $entityManager,
        protected SerializerInterface $serializer,
        protected ValidatorInterface $validator
    ) {}

    private function validateEntity(object $entity): ?JsonResponse
    {
        $errors = $this->validator->validate($entity);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse(
                ['errors' => $errorsString],
                Response::HTTP_BAD_REQUEST
            );
        }

        return null;
    }

    // GET
    #[Route('', name: 'api_authors_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $authors = $this->entityManager->getRepository(Author::class)->findAll();
        $data = $this->serializer->serialize($authors, 'json', ['groups' => 'author:fields']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    // SHOW
    #[Route('/{id}', name: 'api_authors_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Author $author): JsonResponse //Author $author = param Converter automatico | $author = $authorRepository->find($id);
    {
        $data = $this->serializer->serialize($author, 'json', ['groups' => 'author:fields']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    // POST 
    #[Route('', name: 'api_authors_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $author = $this->serializer->deserialize(
            $request->getContent(),  // 1. Recupera il corpo (body) della richiesta HTTP da Json in formato stringa
            Author::class,          // 2. La classe PHP in cui vogliamo convertire i dati JSON
            'json',
            [
                'groups' => 'author:write'
            ]
        );

        if ($errorResponse = $this->validateEntity($author)) {
            return $errorResponse;
        }

        $this->entityManager->persist($author);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($author, 'json', ['groups' => 'author:fields']);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    // PUT 
    #[Route('/{id}', name: 'api_authors_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(Author $author, Request $request): JsonResponse
    {
        $this->serializer->deserialize($request->getContent(), Author::class, 'json', [
            'groups' => 'author:write',
            'object_to_populate' => $author //opzione del metodo deserialize(), che Symfony usa internamente per capire su quale oggetto esistente deve scrivere i dati JSON.
        ]);

        if ($errorResponse = $this->validateEntity($author)) {
            return $errorResponse;
        }

        $this->entityManager->flush();

        $data = $this->serializer->serialize($author, 'json', ['groups' => 'author:fields']);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    // DELETE
    #[Route('/{id}', name: 'api_authors_delete', methods: ['DELETE'])]
    public function delete(Author $author): JsonResponse
    {
        $this->entityManager->remove($author);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT); //204
    }
}
