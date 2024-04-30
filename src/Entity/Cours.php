<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: CoursRepository::class)]
#[UniqueEntity(fields: ['nomCour'], message: 'Ce nom de cours est déjà utilisé.')]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ce champ ne peut pas être vide")]
    #[Assert\Regex(pattern: '/^\D+$/', message: 'Le nom du cours ne peut pas contenir de chiffres.')]
    private ?string $nomCour = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ce champ ne peut pas être vide")]
    #[Assert\Range(min: 30, max: 75, notInRangeMessage: 'La durée doit être comprise entre {{ min }} et {{ max }} minutes')]
    private ?string $Duree = null;

    #[ORM\Column(length: 255)]
    private ?string $Intensite = null;

    #[ORM\Column(length: 255)]
    private ?string $Cible = null;

    #[ORM\Column(length: 255)]
    private ?string $Categorie = null;

    #[ORM\Column(length: 255)]
    private ?string $Objectif = null;

    #[ORM\Column]
    private ?bool $etat = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Ce champ ne peut pas être vide")]
    #[Assert\Range(min: 10, max: 30, notInRangeMessage: 'La capacité doit être comprise entre {{ min }} et {{ max }}')]
    private ?int $capacite = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::BLOB)]
    public $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCour(): ?string
    {
        return $this->nomCour;
    }

    public function setNomCour(string $nomCour): static
    {
        $this->nomCour = $nomCour;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->Duree;
    }

    public function setDuree(string $Duree): static
    {
        $this->Duree = $Duree;

        return $this;
    }

    public function getIntensite(): ?string
    {
        return $this->Intensite;
    }

    public function setIntensite(string $Intensite): static
    {
        $this->Intensite = $Intensite;

        return $this;
    }

    public function getCible(): ?string
    {
        return $this->Cible;
    }

    public function setCible(string $Cible): static
    {
        $this->Cible = $Cible;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->Categorie;
    }

    public function setCategorie(string $Categorie): static
    {
        $this->Categorie = $Categorie;

        return $this;
    }

    public function getObjectif(): ?string
    {
        return $this->Objectif;
    }

    public function setObjectif(string $Objectif): static
    {
        $this->Objectif = $Objectif;

        return $this;
    }

    public function isEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;

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

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }
}
