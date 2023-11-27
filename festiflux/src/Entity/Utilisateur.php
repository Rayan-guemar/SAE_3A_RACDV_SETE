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
use Doctrine\DBAL\Types\Types;

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

    #[ORM\ManyToMany(targetEntity: Festival::class, inversedBy: 'demandesBenevole')]
    #[JoinTable(name: 'demandes_benevole')]
    #[JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'festival_id', referencedColumnName: 'id')]
    private Collection $demandesBenevolat;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Disponibilite::class, orphanRemoval: true)]
    private Collection $disponibilites;
    
    #[ORM\ManyToMany(targetEntity: Tache::class, mappedBy: 'benevoleAffecte')]
    private Collection $taches;

    #[ORM\ManyToMany(targetEntity: Poste::class, mappedBy: 'utilisateurs_aime')]
    private Collection $postes_aime;
    
    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'UtilisateurId', targetEntity: PosteUtilisateurPreferences::class, orphanRemoval: true)]
    private Collection $posteUtilisateurPreferences;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $nomPhotoProfil = null;

    public function __construct() {
        $this->festivals = new ArrayCollection();
        $this->demandeFestivals = new ArrayCollection();
        $this->estBenevole = new ArrayCollection();
        $this->estResponsable = new ArrayCollection();
        $this->demandesBenevolat = new ArrayCollection();
        $this->disponibilites = new ArrayCollection();
        $this->taches = new ArrayCollection();
        $this->postes_aime = new ArrayCollection();
        $this->posteUtilisateurPreferences = new ArrayCollection();
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

    /**
     * @return string|null
     */
    public function getNomPhotoProfil(): ?string
    {
        return $this->nomPhotoProfil;
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

    public function getDescription(): ?string {
        return $this->description;
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

    /**
     * @return Collection<int, Disponibilite>
     */
    public function getDisponibilites(): Collection {
        return $this->disponibilites;
    }

    public function addDisponibilite(Disponibilite $disponibilite): static {
        if (!$this->disponibilites->contains($disponibilite)) {
            $this->disponibilites->add($disponibilite);
            $disponibilite->setUtilisateur($this);
        }
        return $this;
    }

     /* @return Collection<int, Tache>
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTache(Tache $tache): static
    {
        if (!$this->taches->contains($tache)) {
            $this->taches->add($tache);
            $tache->addBenevoleAffecte($this);
        }

        return $this;
    }

    public function removeTache(Tache $tache): static
    {
        if ($this->taches->removeElement($tache)) {
            $tache->removeBenevoleAffecte($this);
        }

        return $this;
    }

    public function removeDemandesBenevolat(Festival $demandesBenevolat): static {
        if ($this->demandesBenevolat->removeElement($demandesBenevolat)) {
            $demandesBenevolat->removeDemandesBenevole($this);
        }
        return $this;
    }

    public function removeDisponibilite(Disponibilite $disponibilite): static {
        if ($this->disponibilites->removeElement($disponibilite)) {
            // set the owning side to null (unless already changed)
            if ($disponibilite->getUtilisateur() === $this) {
                $disponibilite->setUtilisateur(null);
            }
        }
        return $this;
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

    /**
     * @return Collection<int, Poste>
     */
    public function getPostesAime(): Collection
    {
        return $this->postes_aime;
    }

    public function addPostesAime(Poste $postesAime): static
    {
        if (!$this->postes_aime->contains($postesAime)) {
            $this->postes_aime->add($postesAime);
            $postesAime->addUtilisateursAime($this);
        }

        return $this;
    }

    public function removePostesAime(Poste $postesAime): static
    {
        if ($this->postes_aime->removeElement($postesAime)) {
            $postesAime->removeUtilisateursAime($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, PosteUtilisateurPreferences>
     */
    public function getPosteUtilisateurPreferences(): Collection
    {
        return $this->posteUtilisateurPreferences;
    }

    public function addPosteUtilisateurPreference(PosteUtilisateurPreferences $posteUtilisateurPreference): static
    {
        if (!$this->posteUtilisateurPreferences->contains($posteUtilisateurPreference)) {
            $this->posteUtilisateurPreferences->add($posteUtilisateurPreference);
            $posteUtilisateurPreference->setUtilisateurId($this);
        }

        return $this;
    }

    public function removePosteUtilisateurPreference(PosteUtilisateurPreferences $posteUtilisateurPreference): static
    {
        if ($this->posteUtilisateurPreferences->removeElement($posteUtilisateurPreference)) {
            // set the owning side to null (unless already changed)
            if ($posteUtilisateurPreference->getUtilisateurId() === $this) {
                $posteUtilisateurPreference->setUtilisateurId(null);
            }
        }

        return $this;
    }
}
