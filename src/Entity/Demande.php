<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est requis")]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\Type(type: 'numeric', message: "L'âge doit être un nombre")]
    #[Assert\GreaterThan(value: 0, message: "L'âge doit être supérieur à 0")]
    private ?int $Age = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le but est requis")]
    #[Assert\Type(type: 'string', message: "Le but doit être une chaîne de caractères")]
    private ?string $But = null;
    

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le niveau physique est requis")]
    private ?string $niveau_physique = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La maladie chronique est requise")]
    private ?string $maladie_chronique = null;

    #[ORM\Column]
    #[Assert\Type(type: 'numeric', message: "Le nombre d'heures doit être un nombre")]
    #[Assert\GreaterThan(value: 0, message: "Le nombre d'heures doit être supérieur à 0")]
    private ?int $nombreheure = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: ['En attente', 'Approuvé', 'Rejeté'], message: "Veuillez choisir un état valide")]
    private ?string $etat = 'En attente';

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $horaire = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Les jours sont requis")]
    #[Assert\Type(type: 'string', message: "Les jours doivent être une chaîne de caractères")]
    private ?string $lesjours = null;
    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Offre $offre = null;

    // Getters et setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    // Autres getters et setters...

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->Age;
    }

    public function setAge(?int $Age): self
    {
        $this->Age = $Age;

        return $this;
    }

    public function getBut(): ?string
    {
        return $this->But;
    }

    public function setBut(?string $But): self
    {
        $this->But = $But;

        return $this;
    }

    public function getNiveauPhysique(): ?string
    {
        return $this->niveau_physique;
    }

    public function setNiveauPhysique(?string $niveau_physique): self
    {
        $this->niveau_physique = $niveau_physique;

        return $this;
    }

    public function getMaladieChronique(): ?string
    {
        return $this->maladie_chronique;
    }

    public function setMaladieChronique(?string $maladie_chronique): self
    {
        $this->maladie_chronique = $maladie_chronique;

        return $this;
    }

    public function getNombreHeure(): ?int
    {
        return $this->nombreheure;
    }

    public function setNombreHeure(?int $nombreheure): self
    {
        $this->nombreheure = $nombreheure;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getHoraire(): ?\DateTimeInterface
    {
        return $this->horaire;
    }

    public function setHoraire(?\DateTimeInterface $horaire): self
    {
        $this->horaire = $horaire;

        return $this;
    }

    public function getLesjours(): ?string
    {
        return $this->lesjours;
    }

    public function setLesjours(?string $lesjours): self
    {
        $this->lesjours = $lesjours;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    public function setOffre(?Offre $offre): self
    {
        $this->offre = $offre;

        return $this;
    }
}
