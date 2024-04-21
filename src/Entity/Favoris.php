<?php

namespace App\Entity;

use App\Repository\FavorisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavorisRepository::class)]
class Favoris
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $loved = null;

    #[ORM\Column]
    private ?bool $unloved = null;

    #[ORM\ManyToOne(inversedBy: 'evenement')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'favoris')]
    private ?Evenement $evenement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isLoved(): ?bool
    {
        return $this->loved;
    }

    public function setLoved(bool $loved): static
    {
        $this->loved = $loved;

        return $this;
    }

    public function isUnloved(): ?bool
    {
        return $this->unloved;
    }

    public function setUnloved(bool $unloved): static
    {
        $this->unloved = $unloved;

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

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): static
    {
        $this->evenement = $evenement;

        return $this;
    }
}
