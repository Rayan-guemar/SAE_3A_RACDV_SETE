<?php

namespace App\Entity;

use App\Repository\PosteUtilisateurPreferencesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PosteUtilisateurPreferencesRepository::class)]
class PosteUtilisateurPreferences
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $preferencesDegree = null;

    #[ORM\ManyToOne(inversedBy: 'posteUtilisateurPreferences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Poste $posteId = null;

    #[ORM\ManyToOne(inversedBy: 'posteUtilisateurPreferences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $UtilisateurId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPreferencesDegree(): ?int
    {
        return $this->preferencesDegree;
    }

    public function setPreferencesDegree(int $preferencesDegree): static
    {
        $this->preferencesDegree = $preferencesDegree;

        return $this;
    }

    public function getPosteId(): ?Poste
    {
        return $this->posteId;
    }

    public function setPosteId(?Poste $posteId): static
    {
        $this->posteId = $posteId;

        return $this;
    }

    public function getUtilisateurId(): ?Utilisateur
    {
        return $this->UtilisateurId;
    }

    public function setUtilisateurId(?Utilisateur $UtilisateurId): static
    {
        $this->UtilisateurId = $UtilisateurId;

        return $this;
    }
}
