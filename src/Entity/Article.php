<?php

namespace App\Entity;

use App\Entity\Author;

use App\Repository\ArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    // FIELDS
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Id

    #[ORM\Column(length: 70)]
    #[Assert\NotBlank(message: 'Title is required')]
    #[Assert\Length(
        // min: 30,
        min: 5, // // Temporary value for testing purposes only
        max: 70,
        minMessage: 'The title must be at least {{ limit }} characters long',
        maxMessage: 'The title cannot be longer than {{ limit }} characters'
    )]
    private ?string $title = null; // title

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Please, write something')]
    #[Assert\Length(
        // min: 200,
        min: 5, // // Temporary value for testing purposes only
        minMessage: 'Content must be at least {{ limit }} characters long',
    )]
    private ?string $content = null; // content

    #[ORM\Column]
    private ?\DateTime $published_at = null; // published_at

    //MANY2ONE RELATIONSHIP
    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Author $author = null;

    // GETTER AND SETTER
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishedAt(): ?\DateTime
    {
        return $this->published_at;
    }

    public function setPublishedAt(\DateTime $published_at): static
    {
        $this->published_at = $published_at;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): static
    {
        $this->author = $author;

        return $this;
    }
}
