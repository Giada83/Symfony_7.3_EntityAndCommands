<?php

namespace App\Controller\Api;

use App\Entity\Author;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/api', name: 'api_articles_')]
final class ArticleApiController extends AbstractController
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
    #[Route('/articles', name: 'api_articles_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $articles = $this->entityManager->getRepository(Article::class)->findAll();
        $data = $this->serializer->serialize($articles, 'json', [
            'groups' => ['articles:fields', 'articles:author'],
            'max_depth' => 1 // Evita di serializzare eventuali relazioni annidate
        ]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    // SHOW
    #[Route('/articles/{id}', name: 'api_articles_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Article $article): JsonResponse
    {
        $data = $this->serializer->serialize($article, 'json', [
            'groups' => ['articles:fields', 'articles:author'],
            'max_depth' => 1
        ]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    // POST
    #[Route('/authors/{id}/articles', name: 'api_article_create', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function create($id, Request $request): JsonResponse
    {
        $author = $this->entityManager->getRepository(Author::class)->find($id);

        if (!$author) {
            throw $this->createNotFoundException('Author not found');
        }

        $article = $this->serializer->deserialize(
            $request->getContent(),
            Article::class,
            'json',
            ['groups' => 'articles:write']
        );

        $article->setAuthor($author);
        $article->setPublishedAt(new \DateTime());

        if ($errorResponse = $this->validateEntity($article)) {
            return $errorResponse;
        }

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $dataResponse = $this->serializer->serialize($article, 'json', [
            'groups' => ['articles:fields']
        ]);

        return new JsonResponse($dataResponse, Response::HTTP_CREATED, [], true);
    }

    //PUT
    #[Route('/articles/{id}', name: 'api_article_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(Article $article, Request $request): JsonResponse
    {
        $this->serializer->deserialize($request->getContent(), Article::class, 'json', [
            'groups' => 'articles:write',
            'object_to_populate' => $article
        ]);

        if ($errorResponse = $this->validateEntity($article)) {
            return $errorResponse;
        }

        //TODO Aggiungere campo modified_at in entità e qui

        $this->entityManager->flush();

        $data = $this->serializer->serialize($article, 'json', ['groups' => 'articles:fields']);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    //DELETE
    #[Route('/articles/{id}', name: 'api_article_delete', methods: ['DELETE'])]
    public function delete(Article $article): JsonResponse
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
