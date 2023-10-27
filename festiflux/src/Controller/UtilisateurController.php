<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use App\Repository\FestivalRepository;
use App\Repository\TacheRepository;
use App\Repository\UtilisateurRepository;
use App\Service\Ical\IcalBuilder;
use App\Service\Ical\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FlashMessageService;
use App\Service\FlashMessageType;


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

    #[Route('/testical', name: 'app_testical', methods: ['GET'])]
    public function testical() : Response {
        $ical = new IcalBuilder('test');

        $ical->add(new Event(
            'Test4',
            'Super event',
            'Salut à tous c\'est l\'événement test',
            new \DateTime('2023-10-20 12:00:00'),
            new \DateTime('2023-10-20 13:00:00'),
            'Chez ta daronne sah'
        ));

        $ical->build();
        return $this->redirectToRoute('app_festival_all');
    }

    #[Route('/user/{id}/task/{idTask}/add', name: 'app_user_task_add', methods: ['GET'])]
    public function user_task_add(int $id, int $idTask, UtilisateurRepository $user, TacheRepository $tache, EntityManagerInterface $em, FlashMessageService $fm): Response {


           

        $u = $user->find($id);
        $t = $tache->find($idTask);

        
        if($this->getUser() != $t->getFestival()->getOrganisateur() && !$t->getFestival()->getResponsables()->contains($this->getUser())) {
            throw $this->createNotFoundException("Vous n'avez pas les droits pour ajouter un bénévole à cette tache");
        }

        if (!$u) throw $this->createNotFoundException("L'utilisateur n'existe pas");    
        if (!$t) throw $this->createNotFoundException("La tache n'existe pas");

        // TODO : faire une vérification sur les dispo / pref de l'utilisateur

        $u->addTache($t);
        $em->persist($u);
        $em->flush();

        $fm->add(FlashMessageType::SUCCESS, 'Tâche ajoutée');

        return $this->redirectToRoute('home');
    }

}
