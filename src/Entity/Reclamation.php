<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_reclamation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre de la réclamation ne peut pas être vide")]
    #[Assert\Regex(
        pattern: "/^[\p{L}0-9\s]+$/u",
        message: "Le titre de la réclamation ne peut contenir que des lettres, des chiffres et des espaces"
    )]
    private ?string $titre_reclamation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description de la réclamation ne peut pas être vide")]
    #[Assert\Regex(
        pattern: "/^[\p{L}0-9\s]+$/u",
        message: "La description de la réclamation ne peut contenir que des lettres, des chiffres et des espaces"
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateReclamation(): ?\DateTimeInterface
    {
        return $this->date_reclamation;
    }

    public function setDateReclamation(\DateTimeInterface $date_reclamation): static
    {
        $this->date_reclamation = $date_reclamation;

        return $this;
    }

    public function getTitreReclamation(): ?string
    {
        return $this->titre_reclamation;
    }

    public function setTitreReclamation(string $titre_reclamation): static
    {
        $this->titre_reclamation = $titre_reclamation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

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
    public function __toString(): string
    {
        return $this->titre_reclamation ?? '';
    }

    


}
