<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function countAllArticles(): int
    {
        return (int) $this->createQueryBuilder('art')
            ->select('COUNT(art.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function groupArticlesFromPublishedData(): array
    {
        return $this->createQueryBuilder('article')
            ->select('IDENTITY(article.author) AS author_id, article.published_at')
            ->orderBy('author_id', 'ASC')
            ->addOrderBy('article.published_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getPublishDateInterval(): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('art');

        $queryBuilder
            ->select(
                'IDENTITY(art.author) AS author_id, 
                a.name AS author_name,
                    MAX(art.published_at) AS max_date, 
                    MIN(art.published_at) AS min_date, 
                    MAX(art.published_at) - MIN(art.published_at) as publishing_experience'
            )
            ->setMaxResults(5)
            ->join('art.author', 'a')
            ->groupBy('author_id, author_name')
            ->orderBy('publishing_experience', 'DESC');

        return $queryBuilder;
    }

    public function calculateDataInterval(): array
    {
        $rows = $this->getPublishDateInterval()
            ->getQuery()
            ->getArrayResult();

        // foreach ($rows as &$row) {
        //     $max_epoch_date = strtotime($row['max_date']);
        //     $min_epoch_date = strtotime($row['min_date']);

        //     $diff_date = $max_epoch_date - $min_epoch_date;

        //     $epoch_to_date = floor($diff_date / (60 * 60 * 24));

        //     $row['interval_days'] = $epoch_to_date;
        // }

        // usort($rows, function ($a, $b) {
        //     return $b['interval_days'] <=> $a['interval_days'];
        // });

        foreach ($rows as &$row) {
            $string_time = ($row['publishing_experience']);
            $parole = explode(" ", $string_time);
            $prima_parola = $parole[0];
            $seconda_parola = $parole[1] ?? '';
            // var_dump($seconda_parola);
            if ($seconda_parola == 'day' || $seconda_parola == 'days') {
                $row['publishing_experience'] = $prima_parola;
            } else {
                $row['publishing_experience'] = '0';
            }
        }

        return $rows;
    }
}
