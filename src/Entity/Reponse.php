<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "la reponse ne peut pas être vide")]
    #[Assert\Regex(
        pattern: "/^[\p{L}0-9\s]+$/u",
        message: "la reponse ne peut contenir que des lettres, des chiffres et des espaces"
    )]
    private ?string $reponse_reclamation = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reclamation $reclamation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReponseReclamation(): ?string
    {
        return $this->reponse_reclamation;
    }

    public function setReponseReclamation(string $reponse_reclamation): static
    {
        $this->reponse_reclamation = $reponse_reclamation;

        return $this;
    }

    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(Reclamation $reclamation): static
    {
        $this->reclamation = $reclamation;

        return $this;
    }
}
