<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function countArticlesByAuthorQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('a');

        $queryBuilder
            ->select('a AS author, COUNT(art.id) AS article_count')
            ->leftJoin('a.articles', 'art')
            ->groupBy('a.id')
            ->orderBy('article_count', 'DESC')
        ;

        return $queryBuilder;
    }

    public function countArticlesByAuthor(int $page, int $limit): array
    {
        $queryBuilder = $this->countArticlesByAuthorQueryBuilder();

        $queryBuilder->setMaxResults($limit);
        $queryBuilder->setFirstResult(($page - 1) * $limit);

        return $queryBuilder->getQuery()->getResult();

        // $paginator = new Paginator($queryBuilder);
        // return $paginator->getIterator()->getArrayCopy();
    }

    public function countArticlesForBestAuthor(): ?array
    {
        $queryBuilder = $this->countArticlesByAuthorQueryBuilder();

        $queryBuilder->setMaxResults(1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    // public function findTopAuthorLastMonth(): ?array

}
