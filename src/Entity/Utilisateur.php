<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Festival::class)]
    private Collection $festivals;

    #[ORM\OneToMany(mappedBy: 'organisateurFestival', targetEntity: DemandeFestival::class)]
    private Collection $demandeFestivals;

    public function __construct() {
        $this->festivals = new ArrayCollection();
        $this->demandeFestivals = new ArrayCollection();
        $this->creneaux = new ArrayCollection();
        $this->creneauxAffectes = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): static {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): static {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(string $nom): static {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * @return Collection<int, Festival>
     */
    public function getFestivals(): Collection {
        return $this->festivals;
    }

    public function addFestival(Festival $festival): static {
        if (!$this->festivals->contains($festival)) {
            $this->festivals->add($festival);
            $festival->setOrganisateur($this);
        }

        return $this;
    }

    public function removeFestival(Festival $festival): static {
        if ($this->festivals->removeElement($festival)) {
            // set the owning side to null (unless already changed)
            if ($festival->getOrganisateur() === $this) {
                $festival->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DemandeFestival>
     */
    public function getDemandeFestivals(): Collection {
        return $this->demandeFestivals;
    }

    public function addDemandeFestival(DemandeFestival $demandeFestival): static {
        if (!$this->demandeFestivals->contains($demandeFestival)) {
            $this->demandeFestivals->add($demandeFestival);
            $demandeFestival->setOrganisateurFestival($this);
        }

        return $this;
    }

    public function removeDemandeFestival(DemandeFestival $demandeFestival): static {
        if ($this->demandeFestivals->removeElement($demandeFestival)) {
            // set the owning side to null (unless already changed)
            if ($demandeFestival->getOrganisateurFestival() === $this) {
                $demandeFestival->setOrganisateurFestival(null);
            }
        }

        return $this;
    }
}
