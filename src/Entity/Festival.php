<?php

namespace App\Entity;

use App\Repository\FestivalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FestivalRepository::class)]
class Festival
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_festival = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_festival = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToOne(inversedBy: 'festival', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $id_organisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdFestival(): ?int
    {
        return $this->id_festival;
    }

    public function setIdFestival(int $id_festival): static
    {
        $this->id_festival = $id_festival;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getNomFestival(): ?string
    {
        return $this->nom_festival;
    }

    public function setNomFestival(string $nom_festival): static
    {
        $this->nom_festival = $nom_festival;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getIdOrganisateur(): ?Utilisateur
    {
        return $this->id_organisateur;
    }

    public function setIdOrganisateur(Utilisateur $id_organisateur): static
    {
        $this->id_organisateur = $id_organisateur;

        return $this;
    }
}
