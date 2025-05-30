<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\ORM\Mapping as ORM;
#validazioni
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[UniqueEntity('email')] //unique applicato al campo email
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    // validazioni
    #[Assert\NotBlank(message: 'The name of the author cannot be empty')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'The name of the author must be at least {{ limit }} characters long',
        maxMessage: 'The name of the author cannot be longer than {{ limit }} characters',
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    //validazioni
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    #[Assert\NotBlank]
    private ?string $email = null;

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
}
