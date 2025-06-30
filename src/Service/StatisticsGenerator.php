<?php

namespace App\Service;

use App\Repository\AuthorRepository;
use App\Repository\ArticleRepository;

class StatisticsGenerator
{
    public function __construct(
        private AuthorRepository $authorRepository,
        private ArticleRepository $articleRepository
    ) {}

    public function getAuthorWithMostArticles(): ?array
    {
        $author = $this->authorRepository->countArticlesForBestAuthor();
        return $author ?? null;
    }

    public function getLongestPublishingAuthors(): ?array
    {
        // return $this->articleRepository
        //     ->getPublishDateInterval()
        //     ->getQuery()
        //     ->getArrayResult();
        $puiblished_days = $this->articleRepository->calculateDataInterval();
        return $puiblished_days ?? null;
    }


    public function getCounts(): array
    {
        return [
            'authors' => ($authors = $this->authorRepository->countAllAuthors()) !== null ? $authors : 0,
            'articles' => ($articles = $this->articleRepository->countAllArticles()) !== null ? $articles : 0,
        ];
    }
}
