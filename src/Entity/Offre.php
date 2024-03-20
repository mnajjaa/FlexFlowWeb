<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffreRepository::class)]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $specialite = null;

    #[ORM\Column]
    private ?float $tarif_heure = null;

    #[ORM\Column(length: 255)]
    private ?string $etatOffre = null;

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

    public function setTarifHeure(float $tarif_heure): static
    {
        $this->tarif_heure = $tarif_heure;

        return $this;
    }

    public function getEtatOffre(): ?string
    {
        return $this->etatOffre;
    }

    public function setEtatOffre(string $etatOffre): static
    {
        $this->etatOffre = $etatOffre;

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
