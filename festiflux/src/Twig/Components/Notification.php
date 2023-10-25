<?php

namespace App\Twig\Components;

use App\Entity\DemandeFestival;
use App\Repository\DemandeFestivalRepository;
use App\Repository\FestivalRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;


#[AsTwigComponent]
class Notification
{
    private DemandeFestivalRepository $demandeRepo;
    private FestivalRepository $festRepo;
    public int $userId;
    public string $type;

    public function __construct(DemandeFestivalRepository $demandeRepo, FestivalRepository $festRepo)
    {
        $this->demandeRepo = $demandeRepo;
        $this->festRepo = $festRepo;
    }

    function renderNombre(): int
    {
        if($this->type == "demandeFestival"){

            return count($this->demandeRepo->findAll());
            
        } else if ($this->type == "allDemandesBenevolat"){ 
            
            $res = 0;
            foreach($this->festRepo->findBy(['organisateur' => $this->userId]) as $fest){
                $res += count($fest->getDemandesBenevole());
            }
            return $res;
        }
        
    }
}