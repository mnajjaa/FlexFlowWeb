<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OffreRepository::class)]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
    #[ORM\Column(length: 255)]
    #[Assert\Regex(pattern: '/^\D+$/', message: 'Le nom offre ne peut pas contenir de chiffres.')]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $specialite = null;
 
    #[ORM\Column]
#[Assert\Regex(
    pattern: '/^\d{1,4}(\.\d{1,2})?$/',
    message: 'Le tarif par heure doit être un nombre valide avec au plus 2 décimales.'
)]
private ?string $tarif_heure = null;


    #[ORM\Column(length: 255)] 
    private ?string $etat_offre = "en atttente";

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $coach = null;

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

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): static
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getTarifHeure(): ?float
    {
        return $this->tarif_heure;
    }
    
    public function setTarifHeure(float $tarif_heure): self
    {
        $this->tarif_heure = $tarif_heure;
    
        return $this;
    }
    public function getTarif_Heure(): ?float
    {
        return $this->tarif_heure;
    }
    
    public function setTarif_Heure(float $tarif_heure): self
    {
        $this->tarif_heure = $tarif_heure;
    
        return $this;
    }

    public function getEtatOffre(): ?string
    {
        return $this->etat_offre;
    }

    public function setEtatOffre(string $etat_offre): static
    {
        $this->etat_offre = $etat_offre;

        return $this;
    }

    public function getEtat_Offre(): ?string
    {
        return $this->etat_offre;
    }

    public function setEtat_Offre(string $etat_offre): static
    {
        $this->etat_offre = $etat_offre;

        return $this;
    }
    

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCoach(): ?User
    {
        return $this->coach;
    }

    public function setCoach(?User $coach): static
    {
        $this->coach = $coach;

        return $this;
    }

}


