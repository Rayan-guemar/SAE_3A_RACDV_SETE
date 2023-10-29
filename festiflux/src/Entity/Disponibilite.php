<?php

namespace App\Entity;

use App\Repository\DisponibiliteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DisponibiliteRepository::class)]
class Disponibilite {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'disponibilites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'disponibilites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Festival $festival = null;

    #[ORM\ManyToOne(inversedBy: 'disponibilites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Creneaux $creneau = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getFestival(): ?Festival {
        return $this->festival;
    }

    public function setFestival(?Festival $festival): static {
        $this->festival = $festival;

        return $this;
    }

    public function getCreneau(): ?Creneaux {
        return $this->creneau;
    }

    public function setCreneau(?Creneaux $Creneau): static {
        $this->creneau = $Creneau;

        return $this;
    }
}
