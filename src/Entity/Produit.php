<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\ProduitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    #[Assert\Range(min: 0)]
    private ?float $prix = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    #[Assert\Range(min: 0)]
    private ?int $quantite = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    #[Assert\Range(min: 0)]
    private ?int $quantiteVendues = null;

    #[ORM\Column(type: Types::BLOB)]
    public $image = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getQuantiteVendues(): ?int
    {
        return $this->quantiteVendues;
    }

    public function setQuantiteVendues(int $quantiteVendues): static
    {
        $this->quantiteVendues = $quantiteVendues;

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