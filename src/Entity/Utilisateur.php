<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(
    fields: ['email'],
    message: 'Un compte avec cet email existe déjà.',
)]
class Utilisateur implements PasswordAuthenticatedUserInterface {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email]

    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $motDePasse = null;

    #[ORM\OneToMany(mappedBy: 'idOrganisateur', targetEntity: Festival::class)]
    private Collection $festivals;

    public function __construct() {
        $this->festivals = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
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

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): static {
        $this->email = $email;

        return $this;
    }

    public function getMotDePasse(): ?string {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): static {
        $this->motDePasse = $motDePasse;

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
            $festival->setIdOrganisateur($this);
        }

        return $this;
    }

    public function removeFestival(Festival $festival): static {
        if ($this->festivals->removeElement($festival)) {
            // set the owning side to null (unless already changed)
            if ($festival->getIdOrganisateur() === $this) {
                $festival->setIdOrganisateur(null);
            }
        }

        return $this;
    }

    public function getPassword(): ?string {
        return $this->getMotDePasse();
    }

    public function getRoles(): array {
        return ['ROLE_USER'];
    }

    public function getSalt() {
    }

    public function eraseCredentials(): void {
    }
}
