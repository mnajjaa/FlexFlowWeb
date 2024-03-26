<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $Age = null;

    #[ORM\Column(length: 255)]
    private ?string $But = null;

    #[ORM\Column(length: 255)]
    private ?string $niveau_physique = null;

    #[ORM\Column(length: 255)]
    private ?string $maladie_chronique = null;

    #[ORM\Column]
    private ?int $nombreheure = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = 'En attente';

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $horaire = null;

    #[ORM\Column(length: 255)]
    private ?string $lesjours = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Offre $offre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->Age;
    }

    public function setAge(int $Age): static
    {
        $this->Age = $Age;

        return $this;
    }

    public function getBut(): ?string
    {
        return $this->But;
    }

    public function setBut(string $But): static
    {
        $this->But = $But;

        return $this;
    }

   
public function getNiveauPhysique(): ?string
{
    return $this->niveau_physique;
}

public function setNiveauPhysique(string $niveau_physique): self
{
    $this->niveau_physique = $niveau_physique;

    return $this;
}

public function getMaladieChronique(): ?string
{
    return $this->maladie_chronique;
}

public function setMaladieChronique(string $maladie_chronique): self
{
    $this->maladie_chronique = $maladie_chronique;

    return $this;
}

    public function getNombreHeure(): ?int
    {
        return $this->nombreheure;
    }

    public function setNombreHeure(int $nombreheure): static
    {
        $this->nombreheure = $nombreheure;

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

    public function getHoraire(): ?\DateTimeInterface
    {
        return $this->horaire;
    }

    public function setHoraire(\DateTimeInterface $horaire): static
    {
        $this->horaire = $horaire;

        return $this;
    }

    public function getLesjours(): ?string
    {
        return $this->lesjours;
    }

    public function setLesjours(string $lesjours): static
    {
        $this->lesjours = $lesjours;

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

    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    public function setOffre(?Offre $offre): static
    {
        $this->offre = $offre;

        return $this;
    }

    
}
