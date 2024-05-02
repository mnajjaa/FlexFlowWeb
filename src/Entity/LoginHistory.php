<?php

namespace App\Entity;

use App\Repository\LoginHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoginHistoryRepository::class)]
class LoginHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $loginDate = null;

    #[ORM\Column(length: 255)]
    private ?string $ipAdress = null;

    #[ORM\ManyToOne(inversedBy: 'loginHistories')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $navigateur = null;

    #[ORM\Column(length: 255)]
    private ?string $sysExp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLoginDate(): ?\DateTimeInterface
    {
        return $this->loginDate;
    }

    public function setLoginDate(\DateTimeInterface $loginDate): static
    {
        $this->loginDate = $loginDate;

        return $this;
    }

    public function getIpAdress(): ?string
    {
        return $this->ipAdress;
    }

    public function setIpAdress(string $ipAdress): static
    {
        $this->ipAdress = $ipAdress;

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

    public function getNavigateur(): ?string
    {
        return $this->navigateur;
    }

    public function setNavigateur(?string $navigateur): static
    {
        $this->navigateur = $navigateur;

        return $this;
    }

    public function getSysExp(): ?string
    {
        return $this->sysExp;
    }

    public function setSysExp(string $sysExp): static
    {
        $this->sysExp = $sysExp;

        return $this;
    }
}
