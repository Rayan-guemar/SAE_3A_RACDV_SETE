<?php

namespace App\Entity;

use App\Repository\DemandeFestivalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeFestivalRepository::class)]
class DemandeFestival
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomFestival = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateDebutFestival = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateFinFestival = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descriptionFestival = null;

    #[ORM\ManyToOne(inversedBy: 'demandeFestivals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $organisateurFestival = null;

    #[ORM\Column(length: 255)]
    private ?string $lieuFestival = null;

    #[ORM\Column(length: 255)]
    private ?string $afficheFestival = null;

    #[ORM\Column(nullable: true)]
    private ?float $lat = null;

    #[ORM\Column(nullable: true)]
    private ?float $lon = null;

    #[ORM\Column(nullable: true)]
    private ?string $tags;




    /**
     * @return Collection
     */
    public function getTags(): ?string
    {
        return $this->tags;
    }

    /**
     * @param Collection $tags
     */
    public function setTags(string $tags): void
    {
        $this->tags = $tags;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFestival(): ?string
    {
        return $this->nomFestival;
    }

    public function setNomFestival(string $nomFestival): static
    {
        $this->nomFestival = $nomFestival;

        return $this;
    }

    public function getDateDebutFestival(): ?\DateTimeImmutable
    {
        return $this->dateDebutFestival;
    }

    public function setDateDebutFestival(\DateTimeImmutable $dateDebutFestival): static
    {
        $this->dateDebutFestival = $dateDebutFestival;

        return $this;
    }

    public function getDateFinFestival(): ?\DateTimeImmutable
    {
        return $this->dateFinFestival;
    }

    public function setDateFinFestival(\DateTimeImmutable $dateFinFestival): static
    {
        $this->dateFinFestival = $dateFinFestival;

        return $this;
    }

    public function getDescriptionFestival(): ?string
    {
        return $this->descriptionFestival;
    }

    public function setDescriptionFestival(string $descriptionFestival): static
    {
        $this->descriptionFestival = $descriptionFestival;

        return $this;
    }

    public function getOrganisateurFestival(): ?Utilisateur
    {
        return $this->organisateurFestival;
    }

    public function setOrganisateurFestival(?Utilisateur $organisateurFestival): static
    {
        $this->organisateurFestival = $organisateurFestival;

        return $this;
    }

    public function getLieuFestival(): ?string
    {
        return $this->lieuFestival;
    }

    public function setLieuFestival(string $lieuFestival): static
    {
        $this->lieuFestival = $lieuFestival;

        return $this;
    }

    public function getAfficheFestival(): ?string
    {
        return $this->afficheFestival;
    }

    public function setAfficheFestival(string $afficheFestival): static
    {
        $this->afficheFestival = $afficheFestival;

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
