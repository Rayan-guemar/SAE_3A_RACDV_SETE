<?php

namespace App\Entity;

use App\Repository\PostulationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostulationsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Postulations {

    public const STATUS_PENDING = 0;
    public const STATUS_ACCEPTED = 1;
    public const STATUS_REFUSED = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'postulations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Festival $festival = null;

    #[ORM\ManyToOne(inversedBy: 'postulations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0], nullable: false)]
    private ?int $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\OneToMany(mappedBy: 'postulation', targetEntity: Reponse::class, orphanRemoval: true)]
    private Collection $reponses;

    public function __construct() {
        $this->reponses = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getFestival(): ?Festival {
        return $this->festival;
    }

    public function setFestival(?Festival $festival): static {
        $this->festival = $festival;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static {
        $this->date = $date;

        return $this;
    }

    #[ORM\PrePersist]
    public function presetDate(): static {
        $this->date = new \DateTimeImmutable();;

        return $this;
    }

    public function getStatus(): ?int {
        return $this->status;
    }

    public function setStatus(int $status): static {
        $this->status = $status;

        return $this;
    }


    public function getMotif(): ?string {
        return $this->motif;
    }

    public function setMotif(?string $motif): static {
        $this->motif = $motif;

        return $this;
    }

    public function statusToString(): string {
        if ($this->status === self::STATUS_ACCEPTED) {
            return 'Acceptée';
        } else if ($this->status === self::STATUS_REFUSED) {
            return 'Refusée';
        } else {
            return 'En attente';
        }
    }

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponses(): Collection {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): static {

        if (!$this->reponses->contains($reponse)) {
            $this->reponses->add($reponse);
            $reponse->setPostulation($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): static {

        if ($this->reponses->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getPostulation() === $this) {
                $reponse->setPostulation(null);
            }
        }

        return $this;
    }
}
