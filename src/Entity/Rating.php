<?php

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RatingRepository::class)]
class Rating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_cour = null;

    #[ORM\Column]
    private ?int $rating = null;

    #[ORM\Column]
    private ?bool $liked = null;

    #[ORM\Column]
    private ?bool $disliked = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCour(): ?string
    {
        return $this->nom_cour;
    }

    public function setNomCour(string $nom_cour): static
    {
        $this->nom_cour = $nom_cour;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function isLiked(): ?bool
    {
        return $this->liked;
    }

    public function setLiked(bool $liked): static
    {
        $this->liked = $liked;

        return $this;
    }

    public function isDisliked(): ?bool
    {
        return $this->disliked;
    }

    public function setDisliked(bool $disliked): static
    {
        $this->disliked = $disliked;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
