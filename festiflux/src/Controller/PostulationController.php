<?php

namespace App\Controller;

use App\Entity\Festival;
use App\Entity\Postulations;
use App\Entity\Utilisateur;
use App\Repository\PostulationsRepository;
use App\Service\UtilisateurUtils;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostulationController extends AbstractController {
    #[Route('/festival/{id}/postulations', name: 'app_festival_postulations')]
    public function festivalPostulations(#[MapEntity] Festival $festival, PostulationsRepository $postulationRepository): Response {
        $postulations = $postulationRepository->findBy(['festival' => $festival, 'status' => Postulations::STATUS_PENDING]);
        return $this->render('festival/postulations.html.twig', [
            'festival' => $festival,
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

    #[Route('/postulations/{id}', name: 'app_postulations_postulation')]
    public function postulation(#[MapEntity] Postulations $postulation, UtilisateurUtils $utilisateurUtils): Response {

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        $festival = $postulation->getFestival();
        if (!($utilisateurUtils->isOrganisateur($u, $festival))) {
            $this->addFlash('error', 'Vous n\'êtes pas organisateur de ce festival');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        };

        return $this->render('postulations/postulation.html.twig', [
            'postulation' => $postulation,
        ]);
    }
}
