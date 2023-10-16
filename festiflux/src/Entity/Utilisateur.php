<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;







#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
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

    #[ORM\ManyToMany(targetEntity: Festival::class, inversedBy: 'benevoles')]
    #[JoinTable(name: 'est_benevole')]
    #[JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'festival_id', referencedColumnName: 'id')]
    private Collection $estBenevole;

    #[ORM\ManyToMany(targetEntity: Festival::class, inversedBy: 'responsables')]
    #[JoinTable(name: 'est_responsable')]
    #[JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'festival_id', referencedColumnName: 'id')]
    private Collection $estResponsable;

    #[ORM\OneToMany(mappedBy: 'utilisateurDisponible', targetEntity: Creneaux::class)]
    private Collection $disponibilites;

    #[ORM\ManyToMany(targetEntity: Festival::class, inversedBy: 'demandesBenevole')]
    #[JoinTable(name: 'demandes_benevole')]
    #[JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'festival_id', referencedColumnName: 'id')]
    private Collection $demandesBenevolat;


    #[ORM\ManyToMany(targetEntity: Creneaux::class, inversedBy: 'utilisateursAffectes')]
    #[JoinTable(name: 'affectation')]
    #[JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'crenaux_id', referencedColumnName: 'id')]
    private Collection $creneauxAffectes;

    public function __construct() {
        $this->festivals = new ArrayCollection();
        $this->demandeFestivals = new ArrayCollection();
        $this->creneauxAffectes = new ArrayCollection();
        $this->estBenevole = new ArrayCollection();
        $this->estResponsable = new ArrayCollection();
        $this->disponibilites = new ArrayCollection();
        $this->demandesBenevolat = new ArrayCollection();
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

    /**
     * @return Collection<int, Festival>
     */
    public function getEstBenevole(): Collection {
        return $this->estBenevole;
    }

    public function addEstBenevole(Festival $estBenevole): static {
        if (!$this->estBenevole->contains($estBenevole)) {
            $this->estBenevole->add($estBenevole);
        }

        return $this;
    }

    public function removeEstBenevole(Festival $estBenevole): static {
        $this->estBenevole->removeElement($estBenevole);

        return $this;
    }

    /**
     * @return Collection<int, Festival>
     */
    public function getEstResponsable(): Collection {
        return $this->estResponsable;
    }

    public function addEstResponsable(Festival $estResponsable): static {
        if (!$this->estResponsable->contains($estResponsable)) {
            $this->estResponsable->add($estResponsable);
        }

        return $this;
    }

    public function removeEstResponsable(Festival $estResponsable): static {
        $this->estResponsable->removeElement($estResponsable);

        return $this;
    }

    /**
     * @return Collection<int, Creneaux>
     */
    public function getDisponibilites(): Collection {
        return $this->disponibilites;
    }

    public function addDisponibilite(Creneaux $disponibilite): static {
        if (!$this->disponibilites->contains($disponibilite)) {
            $this->disponibilites->add($disponibilite);
            $disponibilite->setUtilisateurDisponible($this);
        }

        return $this;
    }

    public function removeDisponibilite(Creneaux $disponibilite): static {
        if ($this->disponibilites->removeElement($disponibilite)) {
            // set the owning side to null (unless already changed)
            if ($disponibilite->getUtilisateurDisponible() === $this) {
                $disponibilite->setUtilisateurDisponible(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Creneaux>
     */
    public function getCreneauxAffectes(): Collection {
        return $this->creneauxAffectes;
    }

    public function addCreneauxAffecte(Creneaux $creneauxAffecte): static {
        if (!$this->creneauxAffectes->contains($creneauxAffecte)) {
            $this->creneauxAffectes->add($creneauxAffecte);
        }

        return $this;
    }

    public function removeCreneauxAffecte(Creneaux $creneauxAffecte): static {
        $this->creneauxAffectes->removeElement($creneauxAffecte);

        return $this;
    }

    /**
     * @return Collection<int, Festival>
     */
    public function getDemandesBenevolat(): Collection {
        return $this->demandesBenevolat;
    }

    public function addDemandesBenevolat(Festival $demandesBenevolat): static {
        if (!$this->demandesBenevolat->contains($demandesBenevolat)) {
            $this->demandesBenevolat->add($demandesBenevolat);
            $demandesBenevolat->addDemandesBenevole($this);
        }

        return $this;
    }
    public function removeDemandesBenevolat(Festival $demandesBenevolat): static {
        if ($this->demandesBenevolat->removeElement($demandesBenevolat)) {
            $demandesBenevolat->removeDemandesBenevole($this);
        }

        return $this;
    }
}