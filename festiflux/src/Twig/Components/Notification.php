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
            
            $res = 0;
            foreach($this->festRepo->findBy(['organisateur' => $this->userId]) as $fest){
                $res += count($fest->getPostulations());
            }
            return $res;
        } 

        if($this->festId != null) {
            $fest = $this->festRepo->find($this->festId);
            return count($fest->getPostulations());
        }

        return 0;
    }
}