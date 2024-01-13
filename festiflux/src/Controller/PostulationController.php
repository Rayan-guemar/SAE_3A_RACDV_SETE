<?php

namespace App\Controller;

use App\Entity\Festival;
use App\Entity\Postulations;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostulationController extends AbstractController {
    #[Route('/festival/{id}/postulations', name: 'app_postulations')]
    public function festivalPostulations(#[MapEntity] Festival $festival): Response {
        $postulations = $festival->getPostulations();
        return $this->render('festival/postulations.html.twig', [
            'postulations' => $postulations,
        ]);
    }

    #[Route('/postulations', name: 'app_postulations')]
    public function usersPostulations(): Response {
        $user = $this->getUser();
        if (!$user || !$user instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        $postulations = $user->getPostulations();
        return $this->render('postulations/index.html.twig', [
            'postulations' => $postulations,
        ]);
    }
}
