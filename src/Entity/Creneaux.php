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
class Creneaux
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\ManyToOne(inversedBy: 'creneaux')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Festival $festival = null;

    #[ORM\ManyToOne(inversedBy: 'disponibilites')]
    private ?Utilisateur $utilisateurDisponible = null;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, mappedBy: 'creneauxAffectes')]
    #[JoinTable(name: 'affectations')]
    #[JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'creneaux_id', referencedColumnName: 'id')]
    private Collection $utilisateursAffectes;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
        $this->utilisateursAffectes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
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

    public function getUtilisateurDisponible(): ?Utilisateur
    {
        return $this->utilisateurDisponible;
    }

    public function setUtilisateurDisponible(?Utilisateur $utilisateurDisponible): static
    {
        $this->utilisateurDisponible = $utilisateurDisponible;

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateursAffectes(): Collection
    {
        return $this->utilisateursAffectes;
    }

    public function addUtilisateursAffecte(Utilisateur $utilisateursAffecte): static
    {
        if (!$this->utilisateursAffectes->contains($utilisateursAffecte)) {
            $this->utilisateursAffectes->add($utilisateursAffecte);
            $utilisateursAffecte->addCreneauxAffecte($this);
        }

        return $this;
    }

    public function removeUtilisateursAffecte(Utilisateur $utilisateursAffecte): static
    {
        if ($this->utilisateursAffectes->removeElement($utilisateursAffecte)) {
            $utilisateursAffecte->removeCreneauxAffecte($this);
        }

        return $this;
    }
}
