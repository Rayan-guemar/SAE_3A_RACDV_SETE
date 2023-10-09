<?php

namespace App\Entity;

use App\Repository\DemandeBenevoleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeBenevoleRepository::class)]
class DemandeBenevole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'liste_demande_benevoles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?festival $festival_id = null;

    #[ORM\ManyToOne(inversedBy: 'mesDemandes')]
    private ?utilisateur $utilisateur_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFestivalId(): ?festival
    {
        return $this->festival_id;
    }

    public function setFestivalId(?festival $festival_id): static
    {
        $this->festival_id = $festival_id;

        return $this;
    }

    public function getUtilisateurId(): ?utilisateur
    {
        return $this->utilisateur_id;
    }

    public function setUtilisateurId(?utilisateur $utilisateur_id): static
    {
        $this->utilisateur_id = $utilisateur_id;

        return $this;
    }
}
