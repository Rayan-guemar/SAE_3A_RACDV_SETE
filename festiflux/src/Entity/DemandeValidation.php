<?php

namespace App\Entity;

use App\Repository\DemandeValidationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeValidationRepository::class)]
class DemandeValidation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'demandeValidation', targetEntity: Festival::class)]
    private Collection $festival;

    public function __construct()
    {
        $this->festival = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Festival>
     */
    public function getFestival(): Collection
    {
        return $this->festival;
    }

    public function addFestival(Festival $festival): static
    {
        if (!$this->festival->contains($festival)) {
            $this->festival->add($festival);
            $festival->setDemandeValidation($this);
        }

        return $this;
    }

    public function removeFestival(Festival $festival): static
    {
        if ($this->festival->removeElement($festival)) {
            // set the owning side to null (unless already changed)
            if ($festival->getDemandeValidation() === $this) {
                $festival->setDemandeValidation(null);
            }
        }

        return $this;
    }
}
