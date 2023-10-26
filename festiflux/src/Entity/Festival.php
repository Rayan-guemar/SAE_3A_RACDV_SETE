<?php

namespace App\Entity;

use App\Repository\FestivalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use phpDocumentor\Reflection\Types\Boolean;

#[ORM\Entity(repositoryClass: FestivalRepository::class)]
class Festival {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    // #[ORM\ManyToOne(inversedBy: 'festivals')]
    // #[ORM\JoinColumn(nullable: false)]

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'festivals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $organisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column(length: 255)]
    private ?string $affiche = null;

    #[ORM\OneToMany(mappedBy: 'festival', targetEntity: Lieu::class, orphanRemoval: true)]
    private Collection $lieux;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, mappedBy: 'estBenevole')]
    private Collection $benevoles;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, mappedBy: 'estResponsable')]
    private Collection $responsables;


    #[ORM\ManyToMany(targetEntity: Utilisateur::class, mappedBy: 'demandesBenevolat')]
    private Collection $demandesBenevole;

    #[ORM\Column(nullable: true)]
    private ?float $lat = null;

    #[ORM\Column(nullable: true)]
    private ?float $lon = null;


    #[ORM\OneToMany(mappedBy: 'festival', targetEntity: Poste::class, orphanRemoval: true, fetch: 'LAZY')]
    private Collection $postes;

    #[ORM\OneToMany(mappedBy: 'festival', targetEntity: Disponibilite::class, orphanRemoval: true)]
    private Collection $disponibilites;

    #[ORM\Column(nullable: true)]
    private ?int $isArchive;


    public function __construct() {
        $this->lieux = new ArrayCollection();
        $this->benevoles = new ArrayCollection();
        $this->responsables = new ArrayCollection();
        $this->demandesBenevole = new ArrayCollection();
        $this->postes = new ArrayCollection();
        $this->disponibilites = new ArrayCollection();
        $this->isArchive=0;

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

    public function getDateDebut(): ?\DateTimeInterface {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static {
        $this->dateFin = $dateFin;

        return $this;
    }


    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(string $description): static {
        $this->description = $description;

        return $this;
    }

    public function getOrganisateur(): ?Utilisateur {
        return $this->organisateur;
    }

    public function setOrganisateur(?Utilisateur $organisateur): static {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getLieu(): ?string {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static {
        $this->lieu = $lieu;

        return $this;
    }

    public function getAffiche(): ?string {
        return $this->affiche;
    }

    public function setAffiche(string $affiche): static {
        $this->affiche = $affiche;

        return $this;
    }
    /**
     * @return Collection<int, Lieu>
     */
    public function getLieux(): Collection {
        return $this->lieux;
    }

    public function addLieux(Lieu $lieux): static {
        if (!$this->lieux->contains($lieux)) {
            $this->lieux->add($lieux);
            $lieux->setFestival($this);
        }

        return $this;
    }

    public function removeLieux(Lieu $lieux): static {
        if ($this->lieux->removeElement($lieux)) {
            // set the owning side to null (unless already changed)
            if ($lieux->getFestival() === $this) {
                $lieux->setFestival(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getBenevoles(): Collection {
        return $this->benevoles;
    }

    public function addBenevole(Utilisateur $benevole): static {
        if (!$this->benevoles->contains($benevole)) {
            $this->benevoles->add($benevole);
            $benevole->addEstBenevole($this);
        }

        return $this;
    }

    public function removeBenevole(Utilisateur $benevole): static {
        if ($this->benevoles->removeElement($benevole)) {
            $benevole->removeEstBenevole($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getResponsables(): Collection {
        return $this->responsables;
    }

    public function addResponsable(Utilisateur $responsable): static {
        if (!$this->responsables->contains($responsable)) {
            $this->responsables->add($responsable);
            $responsable->addEstResponsable($this);
        }

        return $this;
    }

    public function removeResponsable(Utilisateur $responsable): static {
        if ($this->responsables->removeElement($responsable)) {
            $responsable->removeEstResponsable($this);
        }

        return $this;
    }

    /**

     * @return Collection<int, Utilisateur>
     */
    public function getDemandesBenevole(): Collection {
        return $this->demandesBenevole;
    }

    public function addDemandesBenevole(Utilisateur $demandesBenevole): static {
        if (!$this->demandesBenevole->contains($demandesBenevole)) {
            $this->demandesBenevole->add($demandesBenevole);
            $demandesBenevole->addDemandesBenevolat($this);
        }

        return $this;
    }

    public function removeDemandesBenevole(Utilisateur $demandesBenevole): static {
        if ($this->demandesBenevole->removeElement($demandesBenevole)) {
            $demandesBenevole->removeDemandesBenevolat($this);
        }

        return $this;
    }

    public function getLat(): ?float {
        return $this->lat;
    }

    public function setLat(?float $lat): static {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): ?float {
        return $this->lon;
    }

    public function setLon(?float $lon): static {
        $this->lon = $lon;

        return $this;
    }


    /**
     * @return Collection<int, Poste>
     */
    public function getPostes(): Collection {
        return $this->postes;
    }

    public function addPoste(Poste $poste): static {
        if (!$this->postes->contains($poste)) {
            $this->postes->add($poste);
            $poste->setFestival($this);
        }

        return $this;
    }

    public function removePoste(Poste $poste): static {
        if ($this->postes->removeElement($poste)) {
            // set the owning side to null (unless already changed)
            if ($poste->getFestival() === $this) {
                $poste->setFestival(null);
            }
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
            $disponibilite->setFestival($this);
        }

        return $this;
    }

    public function removeDisponibilite(Disponibilite $disponibilite): static {
        if ($this->disponibilites->removeElement($disponibilite)) {
            // set the owning side to null (unless already changed)
            if ($disponibilite->getFestival() === $this) {
                $disponibilite->setFestival(null);
            }
        }

        return $this;
    }

    public function getIsArchive(): ?string
    {

        return $this->isArchive;
    }

    public function setIsArchive(): void
    {

        $this->isArchive = 1;
    }



}
