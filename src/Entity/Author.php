<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
#validazioni
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
//Unique Constrait
#[UniqueEntity('email')]
#[UniqueEntity(fields: ['name'], message: 'This name is already taken')]

class Author
{
    // FIELDS
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['author:fields'])]
    private ?int $id = null; // id

    #[ORM\Column(length: 50)]
    // validations
    #[Assert\NotBlank(message: 'The name of the author cannot be empty')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'The name of the author must be at least {{ limit }} characters long',
        maxMessage: 'The name of the author cannot be longer than {{ limit }} characters',
    )]
    #[Groups(['author:fields', 'author:write'])]
    private ?string $name = null; //name

    #[ORM\Column(length: 255)]
    //validations
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    #[Assert\NotBlank(message: 'Email is required')]
    #[Groups(['author:fields', 'author:write'])]
    private ?string $email = null; //email

    //ONE2MANY RELATIONSHIP
    /**
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    // GETTER AND SETTER
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setAuthor($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAuthor() === $this) {
                $article->setAuthor(null);
            }
        }

        return $this;
    }
}
