<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use App\Repository\FestivalRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController {

    #[Route('/user/profile/{id}', name: 'app_user_profile')]
    public function profile(int $id, UtilisateurRepository $utilisateurRepository): Response {

        $u = $utilisateurRepository->find($id);
        if (!$u) throw $this->createNotFoundException("L'utilisateur n'existe pas");

        $loggedInUser = $this->getUser();

        $isCurrentUser = $loggedInUser && $loggedInUser->getUserIdentifier() != $u->getUserIdentifier();

        return $this->render('utilisateur/profile.html.twig', [
            'controller_name' => 'UtilisateurController',
            'utilisateur' => $u,
            'isCurrentUser' => $isCurrentUser,
        ]);
    }

    #[Route('/user/festivals', name: 'app_user_festivals', methods: ['GET'])]
    public function user_festivals(FestivalRepository $festivalRepository): Response {
        $u = $this->getUser();

        if (!$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        $fs = $festivalRepository->findBy([
            'organisateur' => $u->getId()
        ]);

        return $this->render('utilisateur/user_festivals.html.twig', [
            'controller_name' => 'UtilisateurController',
            'festivals' => $fs,
        ]);
    }

    #[Route('/user/festivals/{id}/listebenevole', name: 'app_user_liste_benevoles', methods: ['GET'])]
    public function benevole_demande(int $id,FestivalRepository $festivalRepository): Response {
        $u = $this->getUser();

        if (!$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        $fs = $festivalRepository->find($id);

        if ($fs->getOrganisateur() === $u) {
            return $this->render('utilisateur/user_festivals_liste_benevoles.html.twig', [
                'controller_name' => 'UtilisateurController',
                'festivals' => $fs,
                'liste_benevoles' => $fs->getDemandesBenevole()
            ]);
        }else{
            $this->addFlash('error', "Vous n'etez pas autoriser à acceder a cette page");
            return $this->redirectToRoute('home');
        }
    }
}
