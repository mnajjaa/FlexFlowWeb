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
    #[Assert\NotBlank(message: "Le champ ne peut pas être vide.")]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-zA-Z\s\'\-\.\,\!\?\&\$\%\@\#\*\(\)\[\]\{\}àéèç])+[a-zA-Z0-9\s\'\-\.\,\!\?\&\$\%\@\#\*\(\)\[\]\{\}àéèç]*$/u',
        message: "Le nom du produit doit contenir au moins une lettre."
    )]
    private ?string $nom= null;

    #[ORM\Column(length: 500)]
    #[Assert\NotBlank(message: "Le champ ne peut pas être vide.")]
    #[Assert\Regex(
        pattern: '/^[^\r\n]{1,}$/u',
        message: "La description du produit doit contenir au moins une lettre ou un caractère spécial."
    )]
    private ?string $description;
    


    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Le champ ne peut pas être vide.")]
    #[Assert\Type(type: "numeric", message: "Veuillez saisir un prix valide (chiffres uniquement).")]
    #[Assert\Range(min: 0, minMessage: "Le prix doit être supérieur ou égal à zéro.")]
    private ?float $prix;

    #[ORM\Column(length: 255)]
    private ?string $type = null;



    
    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Le champ ne peut pas être vide.")]
    #[Assert\Type(type: "numeric", message: "Veuillez saisir une quantité valide (chiffres uniquement).")]
    #[Assert\Range(min: 0, minMessage: "La quantité doit être supérieure ou égale à zéro.")]
    private ?int $quantite;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Le champ ne peut pas être vide.")]
    #[Assert\Type(type: "numeric", message: "Veuillez saisir une quantité vendue valide (chiffres uniquement).")]
    #[Assert\Range(min: 0, minMessage: "La quantité vendue doit être supérieure ou égale à zéro.")]
    private ?int $quantiteVendues;


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