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

    #[Route('/top-author', name: 'top-author', methods: ['GET'])]
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

    // #[Route('/longest-publishing', name: 'longest-publishing', methods: ['GET'])]
    // public function LongestPublishingAuthors(): JsonResponse
    // {
    //     $data = [
    //         'longest-publishing' => $this->statisticsGenerator->getLongestPublishingAuthors()
    //     ];
    //     return new JsonResponse($data);
    // }
    #[Route('/longest-publishing', name: 'longest-publishing', methods: ['GET'])]
    public function LongestPublishingAuthors(): JsonResponse
    {
        $authorsData = $this->statisticsGenerator->getLongestPublishingAuthors();

        // Prepara i dati per il grafico
        $chartData = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Giorni di attività',
                    'data' => [],
                    // 'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    // 'borderColor' => 'rgba(54, 162, 235, 1)',
                    // 'borderWidth' => 1
                ]
            ]
        ];

        foreach ($authorsData as $author) {
            $chartData['labels'][] = $author['author_id'] ?? 'Autore sconosciuto';
            $chartData['datasets'][0]['data'][] = (int)$author['publishing_experience'];
        }

        return new JsonResponse($chartData);
    }
}
