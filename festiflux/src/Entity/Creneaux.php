<?php

namespace App\Entity;

use App\Repository\CreneauxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\InverseJoinColumn;


#[ORM\Entity(repositoryClass: CreneauxRepository::class)]
class Creneaux {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\OneToMany(mappedBy: 'creneau', targetEntity: Disponibilite::class, orphanRemoval: true)]
    private Collection $disponibilites;

    #[ORM\OneToOne(mappedBy: 'crenaux', cascade: ['persist', 'remove'])]
    private ?Tache $tache = null;

    #[ORM\OneToOne(mappedBy: 'creneau', cascade: ['persist', 'remove'])]
    private ?Indisponibilite $indisponibilite = null;


    public function __construct() {
        $this->disponibilites = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
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

    public function getTache(): ?Tache {
        return $this->tache;
    }

    public function setTache(Tache $tache): static {
        // set the owning side of the relation if necessary
        if ($tache->getCrenaux() !== $this) {
            $tache->setCrenaux($this);
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
            $disponibilite->setCreneau($this);
        }

        return $this;
    }

    public function removeDisponibilite(Disponibilite $disponibilite): static {
        if ($this->disponibilites->removeElement($disponibilite)) {
            // set the owning side to null (unless already changed)
            if ($disponibilite->getCreneau() === $this) {
                $disponibilite->setCreneau(null);
            }
        }

        return $this;
    }

    public function getIndisponibilite(): ?Indisponibilite
    {
        return $this->indisponibilite;
    }

    public function setIndisponibilite(Indisponibilite $indisponibilite): static
    {
        // set the owning side of the relation if necessary
        if ($indisponibilite->getCreneau() !== $this) {
            $indisponibilite->setCreneau($this);
        }

        $this->indisponibilite = $indisponibilite;

        return $this;
    }
}
