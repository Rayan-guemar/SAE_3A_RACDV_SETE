<?php

namespace App\Twig\Components;

use App\Entity\Postulations;
use App\Repository\ValidationRepository;
use App\Repository\FestivalRepository;
use App\Repository\PostulationsRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Notification
{   
    private ValidationRepository $validRepo;
    private FestivalRepository $festRepo;
    private PostulationsRepository $postRepo;
    public ?int $userId;
    public string $type;
    public ?int $festId;

    public function __construct(ValidationRepository $validRepo, FestivalRepository $festRepo, PostulationsRepository $postRepo)
    {
        $this->validRepo = $validRepo;
        $this->postRepo = $postRepo;
        $this->festRepo = $festRepo;
    }

    function renderNombre(): int
    {
        if($this->type == "demandeFestival"){
          
            $validations = $this->validRepo->findBy(['status' => 0]);
            return count($validations);   
          
        } else if ($this->type == "allDemandesBenevolat" && $this->userId != null){ 
            
            $postulations = $this->postRepo->findBy(['status' => 0, 'utilisateur' => $this->userId]);
            return count($postulations);
            
        } else if ($this->type == "allPostulations" && $this->userId != null){ 
            
            $postulations = $this->postRepo->findBy(['status' => 0]);
            // filtre les postulations en laissant celles qui ne correspondent pas au même user
            $postulations = array_filter($postulations, function(Postulations $post) {
                return $post->getUtilisateur()->getId() != $this->userId;
            });
            return count($postulations);
            
        } 

        if($this->festId != null) {
            $fest = $this->festRepo->find($this->festId);
            $postulations = $fest->getPostulations()->filter(function(Postulations $post) {
                return $post->getStatus() == 0;
            });
            return count($postulations);
        }

        return 0;
    }
}