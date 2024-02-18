<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reponses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?QuestionBenevole $question = null;


    #[ORM\Column(length: 255)]
    private ?string $contenue = null;

    #[ORM\ManyToOne(inversedBy: 'reponses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Postulations $postulation = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getQuestion(): ?QuestionBenevole {
        return $this->question;
    }

    public function setQuestion(?QuestionBenevole $question): static {
        $this->question = $question;

        return $this;
    }

    public function getContenue(): ?string {
        return $this->contenue;
    }

    public function setContenue(string $contenue): static {
        $this->contenue = $contenue;

        return $this;
    }

    public function getPostulation(): ?Postulations
    {
        return $this->postulation;
    }

    public function setPostulation(?Postulations $postulation): static
    {
        $this->postulation = $postulation;

        return $this;
    }
}
