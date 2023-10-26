<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;

#[ORM\Entity(repositoryClass: TacheRepository::class)]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Festival $festival = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    private ?Lieu $lieu = null;

    #[ORM\OneToOne(inversedBy: 'tache', cascade: ['persist', 'remove'])]
    #[JoinTable(name: 'affectation')]
    #[JoinColumn(name: 'crenaux_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'tache_id', referencedColumnName: 'id')]
    private ?Creneaux $crenaux = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, inversedBy: 'taches')]
    private Collection $benevoleAffecte;

    public function __construct()
    {
        $this->benevoleAffecte = new ArrayCollection();
    }

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

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getCrenaux(): ?Creneaux
    {
        return $this->crenaux;
    }

    public function setCrenaux(Creneaux $crenaux): static
    {
        $this->crenaux = $crenaux;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getBenevoleAffecte(): Collection
    {
        return $this->benevoleAffecte;
    }

    public function addBenevoleAffecte(Utilisateur $benevoleAffecte): static
    {
        if (!$this->benevoleAffecte->contains($benevoleAffecte)) {
            $this->benevoleAffecte->add($benevoleAffecte);
        }

        return $this;
    }

    public function removeBenevoleAffecte(Utilisateur $benevoleAffecte): static
    {
        $this->benevoleAffecte->removeElement($benevoleAffecte);

        return $this;
    }
}
