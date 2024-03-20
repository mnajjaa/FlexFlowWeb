<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $card_info = null;

    #[ORM\Column]
    private ?int $mm = null;

    #[ORM\Column]
    private ?int $yy = null;

    #[ORM\Column]
    private ?int $cvc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCardInfo(): ?string
    {
        return $this->card_info;
    }

    public function setCardInfo(string $card_info): static
    {
        $this->card_info = $card_info;

        return $this;
    }

    public function getMm(): ?int
    {
        return $this->mm;
    }

    public function setMm(int $mm): static
    {
        $this->mm = $mm;

        return $this;
    }

    public function getYy(): ?int
    {
        return $this->yy;
    }

    public function setYy(int $yy): static
    {
        $this->yy = $yy;

        return $this;
    }

    public function getCvc(): ?int
    {
        return $this->cvc;
    }

    public function setCvc(int $cvc): static
    {
        $this->cvc = $cvc;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }
}
