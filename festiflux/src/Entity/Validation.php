<?php

namespace App\Entity;

use App\Repository\ValidationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ValidationRepository::class)]
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

    public function accept() {
        $this->setStatus(1);
    }

    public function reject() {
        $this->setStatus(-1);
    }
}
