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

    #[ORM\OneToMany(mappedBy: 'festival', targetEntity: Creneaux::class, orphanRemoval: true)]
    private Collection $creneaux;

    #[ORM\OneToMany(mappedBy: 'festival', targetEntity: Tache::class, orphanRemoval: true)]
    private Collection $taches;

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

    public function __construct() {
        $this->creneaux = new ArrayCollection();
        $this->taches = new ArrayCollection();
        $this->lieux = new ArrayCollection();
        $this->benevoles = new ArrayCollection();
        $this->responsables = new ArrayCollection();
        $this->demandesBenevole = new ArrayCollection();
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
     * @return Collection<int, Creneaux>
     */
    public function getCreneaux(): Collection {
        return $this->creneaux;
    }

    public function addCreneaux(Creneaux $creneaux): static {
        if (!$this->creneaux->contains($creneaux)) {
            $this->creneaux->add($creneaux);
            $creneaux->setFestival($this);
        }

        return $this;
    }

    public function removeCreneaux(Creneaux $creneaux): static {
        if ($this->creneaux->removeElement($creneaux)) {
            // set the owning side to null (unless already changed)
            if ($creneaux->getFestival() === $this) {
                $creneaux->setFestival(null);
            }
        }

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
            $tach->setFestival($this);
        }

        return $this;
    }

    public function removeTach(Tache $tach): static {
        if ($this->taches->removeElement($tach)) {
            // set the owning side to null (unless already changed)
            if ($tach->getFestival() === $this) {
                $tach->setFestival(null);
            }
        }

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
        if ($this->responsables->removeElement($demandesBenevole)) {
            $demandesBenevole->removeDemandesBenevolat($this);
        }

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(?float $lon): static
    {
        $this->lon = $lon;

        return $this;
    }
}
