<?php

namespace App\Controller\Api;

use App\Service\StatisticsGenerator;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/statistics', name: 'api_statistics_')]
final class StatisticsApiController extends AbstractController
{

    public function __construct(
        private StatisticsGenerator $statisticsGenerator,
        private SerializerInterface $serializer,
    ) {}

    #[Route('/top-author', name: 'api_top-author_statistics', methods: ['GET'])]
    public function getStatistics(): JsonResponse
    {
        $data = [
            'topAuthor' => $this->statisticsGenerator->getAuthorWithMostArticles()
        ];

        return new JsonResponse(
            data: $this->serializer->serialize($data, 'json', ['groups' => 'author:fields']),
            json: true,
        );
    }

    #[Route('/longest-publishing', name: 'longest-publishing', methods: ['GET'])]
    public function LongestPublishingAuthors(): JsonResponse
    {
        $data = [
            'longest-publishing' => $this->statisticsGenerator->getLongestPublishingAuthors()
        ];
        return new JsonResponse($data);
    }
}
