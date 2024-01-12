<?php

namespace App\Entity;

use App\Repository\QuestionBenevoleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionBenevoleRepository::class)]
class QuestionBenevole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\ManyToOne(inversedBy: 'questionBenevoles')]
    private ?Festival $festival = null;

    #[ORM\Column(length: 255)]
    private ?string $typeReponse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

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

    public function getTypeReponse(): ?string
    {
        return $this->typeReponse;
    }

    public function setTypeReponse(string $typeReponse): static
    {
        $this->typeReponse = $typeReponse;

        return $this;
    }
}
