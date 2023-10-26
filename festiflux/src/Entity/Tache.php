<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;

#[ORM\Entity(repositoryClass: TacheRepository::class)]
class Tache {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    private ?Lieu $lieu = null;

    #[ORM\OneToOne(inversedBy: 'tache', cascade: ['persist', 'remove'])]
    #[JoinTable(name: 'affectation')]
    #[JoinColumn(name: 'crenaux_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'tache_id', referencedColumnName: 'id')]
    private ?Creneaux $crenaux = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Poste $poste = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $nombre_benevole = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getLieu(): ?Lieu {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): static {
        $this->lieu = $lieu;

        return $this;
    }

    public function getCrenaux(): ?Creneaux {
        return $this->crenaux;
    }

    public function setCrenaux(Creneaux $crenaux): static {
        $this->crenaux = $crenaux;

        return $this;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(string $nom): static {
        $this->nom = $nom;

        return $this;
    }

    public function getPoste(): ?Poste {
        return $this->poste;
    }

    public function setPoste(?Poste $poste): static {
        $this->poste = $poste;

        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): static {
        $this->description = $description;

        return $this;
    }

    public function getNombreBenevole(): ?int {
        return $this->nombre_benevole;
    }

    public function setNombreBenevole(int $nombre_benevole): static {
        $this->nombre_benevole = $nombre_benevole;

        return $this;
    }
}
