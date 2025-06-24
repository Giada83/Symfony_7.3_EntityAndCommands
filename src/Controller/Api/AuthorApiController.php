<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\AuthorRepository;
use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/authors', name: 'api_authors_')] #rotta globale
final class AuthorApiController extends AbstractController
{
    #index
    #[Route('/list', name: 'list', methods: ['GET'])]
    public function apiList(AuthorRepository $repo): JsonResponse
    {
        $authors = $repo->findAll();
        return $this->json($authors);
    }
}
