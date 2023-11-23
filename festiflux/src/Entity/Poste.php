<?php

namespace App\Entity;

use App\Repository\PosteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PosteRepository::class)]
class Poste {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'postes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Festival $festival = null;

    #[ORM\OneToMany(mappedBy: 'poste', targetEntity: Tache::class, orphanRemoval: true)]
    private Collection $taches;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, inversedBy: 'postes_aime')]
    private Collection $utilisateurs_aime;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $couleur = null;

    #[ORM\OneToMany(mappedBy: 'posteId', targetEntity: PosteUtilisateurPreferences::class, orphanRemoval: true)]
    private Collection $posteUtilisateurPreferences;

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function __construct() {
        $this->taches = new ArrayCollection();
        $this->utilisateurs_aime = new ArrayCollection();
        $this->posteUtilisateurPreferences = new ArrayCollection();
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

    public function getFestival(): ?Festival {
        return $this->festival;
    }

    public function setFestival(?Festival $festival): static {
        $this->festival = $festival;
        return $this;
    }

    /**
     * @return Collection<int, Tache>
     */
    public function getTaches(): Collection {
        return $this->taches;
    }

    public function addTach(Tache $tach): static {
        if (!$this->taches->contains($tach)) {
            $this->taches->add($tach);
            $tach->setPoste($this);
        }

        return $this;
    }

    public function removeTach(Tache $tach): static {
        if ($this->taches->removeElement($tach)) {
            // set the owning side to null (unless already changed)
            if ($tach->getPoste() === $this) {
                $tach->setPoste(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateursAime(): Collection
    {
        return $this->utilisateurs_aime;
    }

    public function addUtilisateursAime(Utilisateur $utilisateursAime): static
    {
        if (!$this->utilisateurs_aime->contains($utilisateursAime)) {
            $this->utilisateurs_aime->add($utilisateursAime);
        }

        return $this;
    }

    public function removeUtilisateursAime(Utilisateur $utilisateursAime): static
    {
        $this->utilisateurs_aime->removeElement($utilisateursAime);

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): static
    {
        $this->couleur = $couleur;

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
            $posteUtilisateurPreference->setPosteId($this);
        }

        return $this;
    }

    public function removePosteUtilisateurPreference(PosteUtilisateurPreferences $posteUtilisateurPreference): static
    {
        if ($this->posteUtilisateurPreferences->removeElement($posteUtilisateurPreference)) {
            // set the owning side to null (unless already changed)
            if ($posteUtilisateurPreference->getPosteId() === $this) {
                $posteUtilisateurPreference->setPosteId(null);
            }
        }

        return $this;
    }
}
