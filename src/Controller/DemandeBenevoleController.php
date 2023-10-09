<?php

namespace App\Controller;

use App\Entity\DemandeBenevole;
use App\Entity\Festival;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DemandeBenevoleController extends AbstractController
{
    #[Route('/demandebenevole/{id}/add', options: ["expose"=>true],name: 'app_demandebenevole_add')]
    public function addBenevole(#[MapEntity] ?Festival $fest,EntityManagerInterface $em)
    {
        if($this->isGranted('ROLE_USER')) {
            $demandeBenevole = new DemandeBenevole();
            $utilisateur = $this->getUser();
            $demandeBenevole->setUtilisateurId($utilisateur);
            $demandeBenevole->setFestivalId($fest);
            $em->persist($demandeBenevole);
            $em->flush();
        }
        else{
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }
        $this->addFlash('success', 'Vous etes sur la liste des demandes !');
        return $this->redirectToRoute('home');
    }
}