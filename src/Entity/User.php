<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true,nullable: true)]
    // #[Assert\Length(min: 3, max: 180)]
    // #[Assert\NotBlank]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array  $roles=[] ;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    // #[Assert\Length(min: 6)]
    // #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(length: 255)]
    // #[Assert\NotBlank]
    // #[Assert\Regex(
    //     pattern: "/^[a-zA-Z\s]*$/",
    // )]
    private ?string $nom = null;

    
    #[ORM\Column]
    // #[Assert\Length(min: 8, max: 8)]
    // #[Assert\Regex(pattern: "/^0[1-9][0-9]{8}$/")]  
    // #[Assert\NotBlank]
    private ?int $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mfaSecret = null;

    #[ORM\Column(nullable: true)]
    private ?bool $mfaEnabled = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $mdp_exp = null;

    #[ORM\OneToMany(targetEntity: LoginHistory::class, mappedBy: 'user')]
    private Collection $loginHistories;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;


    #[ORM\OneToMany(targetEntity: Favoris::class, mappedBy: 'user')]
    private Collection $evenement;

    public function __construct()
    {
        $this->loginHistories = new ArrayCollection();
        $this->evenement = new ArrayCollection();

    }

    

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

   
    public function getroles(): array
    {
        
        

        return $this->roles;
    }

    public function setroles(array $role): self
    {
        $this->roles = $role;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
        
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
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

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getMfaSecret(): ?string
    {
        return $this->mfaSecret;
    }

    public function setMfaSecret(?string $mfaSecret): static
    {
        $this->mfaSecret = $mfaSecret;

        return $this;
    }

    public function isMfaEnabled(): ?bool
    {
        return $this->mfaEnabled;
    }

    public function setMfaEnabled(?bool $mfaEnabled): static
    {
        $this->mfaEnabled = $mfaEnabled;

        return $this;
    }

    public function getMdpExp(): ?\DateTimeInterface
    {
        return $this->mdp_exp;
    }

    public function setMdpExp(?\DateTimeInterface $mdp_exp): static
    {
        $this->mdp_exp = $mdp_exp;

        return $this;
    }
 /**
     * @return Collection<int, Favoris>
     */
    public function getEvenement(): Collection
    {
        return $this->evenement;
    }

    public function addEvenement(Favoris $evenement): static
    {
        if (!$this->evenement->contains($evenement)) {
            $this->evenement->add($evenement);
            $evenement->setUser($this);
        }

        return $this;
    }

    public function removeEvenement(Favoris $evenement): static
    {
        if ($this->evenement->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getUser() === $this) {
                $evenement->setUser(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->getUsername(); // Remplacez getUsername() par la méthode qui retourne une représentation de l'utilisateur que vous souhaitez afficher.
    }

    /**
     * @return Collection<int, LoginHistory>
     */
    public function getLoginHistories(): Collection
    {
        return $this->loginHistories;
    }

    public function addLoginHistory(LoginHistory $loginHistory): static
    {
        if (!$this->loginHistories->contains($loginHistory)) {
            $this->loginHistories->add($loginHistory);
            $loginHistory->setUser($this);
        }

        return $this;
    }

    public function removeLoginHistory(LoginHistory $loginHistory): static
    {
        if ($this->loginHistories->removeElement($loginHistory)) {
            // set the owning side to null (unless already changed)
            if ($loginHistory->getUser() === $this) {
                $loginHistory->setUser(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}