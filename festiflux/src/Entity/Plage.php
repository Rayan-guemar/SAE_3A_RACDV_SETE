<?php

namespace App\Entity;

use App\Repository\PlageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlageRepository::class)]
class Plage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'plages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Festival $festival = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Creneaux $creneau = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFestival(): ?Festival
    {
        return $this->festival;
    }

    public function setFestival(?Festival $festival): static
    {
        $this->festival = $festival;

        return $this;
    }

    public function getCreneau(): ?Creneaux
    {
        return $this->creneau;
    }

    public function setCreneau(Creneaux $creneau): static
    {
        $this->creneau = $creneau;

        return $this;
    }
}
