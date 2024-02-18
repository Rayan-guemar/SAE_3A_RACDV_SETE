<?php

namespace App\Twig\Components;

use App\Repository\ValidationRepository;
use App\Repository\FestivalRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Notification
{   
    private ValidationRepository $validRepo;
    private FestivalRepository $festRepo;
    public ?int $userId;
    public string $type;
    public ?int $festId;

    public function __construct(ValidationRepository $validRepo, FestivalRepository $festRepo)
    {
        $this->validRepo = $validRepo;
    }

    function renderNombre(): int
    {
        if($this->type == "demandeFestival"){
          
            $validations = $this->validRepo->findBy(['status' => 0]);

            return count($validations);   
          
        } else if ($this->type == "allDemandesBenevolat" && $this->userId != null){ 
            
            $res = 0;
            foreach($this->festRepo->findBy(['organisateur' => $this->userId]) as $fest){
                $res += count($fest->getDemandesBenevole());
            }
            return $res;
        } 
        if($this->festId != null) {
            $fest = $this->festRepo->find($this->festId);
            return count($fest->getDemandesBenevole());
        }

        return 0;
    }
}