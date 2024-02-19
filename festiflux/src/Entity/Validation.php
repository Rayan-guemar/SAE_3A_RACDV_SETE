<?php

namespace App\Entity;

use App\Repository\ValidationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ValidationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Validation {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'validations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Festival $festival = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

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

    public function getStatus(): ?int {
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

    public function accept() {
        $this->setStatus(1);
        $this->festival->setValid(1);
    }

    public function reject() {
        $this->setStatus(-1);
        $this->festival->setValid(0);
    }

    public function getStatusToString() {
        switch ($this->getStatus()) {
            case 0:
                return "En attente";
            case 1:
                return "Accepté";
            case -1:
                return "Refusé";
        }
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
}
