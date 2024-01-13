<?php

namespace App\Entity;

use App\Repository\PostulationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostulationsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Postulations {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'postulations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Festival $festival = null;

    #[ORM\ManyToOne(inversedBy: 'postulations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0], nullable: false)]
    private ?int $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getFestival(): ?Festival {
        return $this->festival;
    }

    public function setFestival(?Festival $festival): static {
        $this->festival = $festival;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static {
        $this->date = $date;

        return $this;
    }

    #[ORM\PrePersist]
    public function presetDate(): static {
        $this->date = new \DateTimeImmutable();;

        return $this;
    }

    public function isStatus(): ?int {
        return $this->status;
    }

    public function setStatus(int $status): static {
        $this->status = $status;

        return $this;
    }


    public function getMotif(): ?string {
        return $this->motif;
    }

    public function setMotif(?string $motif): static {
        $this->motif = $motif;

        return $this;
    }

    public function statusToString(): string {
        if ($this->status === 0) {
            return 'En attente';
        } else if ($this->status === 1) {
            return 'Acceptée';
        } else if ($this->status === 2) {
            return 'Refusée';
        } else {
            return '/';
        }
    }
}
