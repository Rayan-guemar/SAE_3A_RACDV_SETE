<?php

namespace App\Controller;

use App\Entity\Festival;
use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use App\Form\ModifierFestivalType;
use App\Form\ModifierProfilType;
use App\Repository\FestivalRepository;
use App\Repository\PosteRepository;
use App\Repository\PosteUtilisateurPreferencesRepository;
use App\Repository\TacheRepository;
use App\Repository\UtilisateurRepository;
use App\Service\Ical\IcalBuilder;
use App\Service\Ical\Event;
use App\Service\UtilisateurUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\FlashMessageService;
use App\Service\FlashMessageType;
use Symfony\Component\Validator\Constraints\Date;
use App\Service\UtilisateurManagerInterface;

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
            return $this->redirectToRoute('app_auth_login');
        }

        $ofs = $festivalRepository->findBy([
            'organisateur' => $u->getId()
        ]);

        $vfs = ($u->getEstBenevole())->getValues();

        return $this->render('utilisateur/user_festivals.html.twig', [
            'controller_name' => 'UtilisateurController',
            'festivals' => $ofs,
            'volenteerFestivals' => $vfs,
        ]);
    }

    #[Route('/icalLink/{idFest}', name: 'app_icalLink', methods: ['GET'])]
    public function testeventical(int $idFest, PosteRepository $posteRepository, FestivalRepository $festivalRepository, TacheRepository $tacheRepository, UtilisateurUtils $utilisateurUtils, MailerInterface $mailer): Response {
        $currentUser = $this->getUser();
        $fest = $festivalRepository->find($idFest);
        if (!$currentUser instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }
        if (!$fest instanceof Festival) {
            $this->addFlash('error', 'Ce festival n\'existe pas');
            return $this->redirectToRoute('home');
        }
        if ($utilisateurUtils->isBenevole($currentUser, $fest)) {
            $taches = $currentUser->getTaches();
            $ical = new IcalBuilder('benevoleCalendar');
            foreach ($taches as $t) {
                $ical->add(new Event(
                    "Event" . $t->getId(),
                    $t->getPoste()->getNom(),
                    $t->getRemarque(),
                    $t->getCrenaux()->getDateDebut(),
                    $t->getCrenaux()->getDateFin(),
                    $t->getLieu()->getNomLieu(),
                    null
                ));
            }
            $ical->build();

            /*faut le lui envoyer par mail*/
            $email = (new Email())
                ->from('administration@festiflux.fr')
                ->to($currentUser->getEmail())
                ->subject('Votre fichier ICS Bénévole')
                ->attach(fopen('icals/' . $ical->getFilename() . '.ics', 'r'), $ical->getFilename() . '.ics')
                ->html('<p>Voici votre fichier ics des taches qui vous sont assignées en tant que bénévole pour le festival ' . $fest->getNom() . '.' . ' <br><br> Cliquez <a href="https://www.frandroid.com/comment-faire/tutoriaux/tutoriels-pc/1558958_comment-ajouter-un-evenement-icalendar-ics-a-google-agenda"  > ici </a> pour avoir un tuto pas à pas sur "Comment ajouter un. ics à Google Agenda". </p>');

            $mailer->send($email);
            $this->addFlash('success', 'Votre allez recevoir un mail avec en pj le fichier ics.');
        }
        if ($utilisateurUtils->isResponsable($currentUser, $fest) || $utilisateurUtils->isOrganisateur($currentUser, $fest)) {
            $posts = $posteRepository->findBy(["festival" => $fest]);
            $ical = new IcalBuilder('ResponsableCaledar');
            foreach ($posts as $p) {
                $taches = $p->getTaches();
                foreach ($taches as $t) {
                    if ($t->getPoste()->getFestival() === $fest) {
                        $ical->add(new Event(
                            "Event" . $t->getId(),
                            $t->getPoste()->getNom(),
                            $t->getRemarque(),
                            $t->getCrenaux()->getDateDebut(),
                            $t->getCrenaux()->getDateFin(),
                            $t->getLieu()->getNomLieu(),
                            $t->getNombreBenevole()
                        ));
                    }
                }
            }
            $ical->build();
            /*faut le lui envoyer par mail*/
            $email = (new Email())
                ->from('administration@festiflux.fr')
                ->to($currentUser->getEmail())
                ->subject('Votre fichier ICS Responsible')
                ->attach(fopen('icals/' . $ical->getFilename() . '.ics', 'r'), $ical->getFilename() . '.ics')
                ->html('<p>Voici votre fichier ics des taches pour le festival ' . $fest->getNom() . '.' . ' <br><br> Cliquez <a href="https://www.frandroid.com/comment-faire/tutoriaux/tutoriels-pc/1558958_comment-ajouter-un-evenement-icalendar-ics-a-google-agenda"  > ici </a> pour avoir un tuto pas à pas sur "Comment ajouter un. ics à Google Agenda". </p>');

            $mailer->send($email);
            $this->addFlash('success', 'Votre allez recevoir un mail avec en pj le fichier ics.');
        }



        return $this->redirectToRoute('app_festival_all');
    }

    #[Route('/user/profile/{id}/edit', name: 'app_profile_edit')]
    public function edit(UtilisateurRepository $repository, #[MapEntity] Utilisateur $utilisateur, Request $request, EntityManagerInterface $em,UtilisateurManagerInterface $utilisateurManager): Response {

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }


        $form = $this->createForm(ModifierProfilType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photoProfil=$form["fichierPhotoProfil"]->getData();
            $utilisateurManager->processNewUtilisateur($utilisateur,$photoProfil);
            $em->flush();
            $this->addFlash('success', 'Votre profil a été modifié avec succès.');
            return $this->redirectToRoute('app_user_profile', ['id' => $utilisateur->getId()]);
        }

        return $this->render('utilisateur/modifierProfil.html.twig', [
            'controller_name' => 'UtilisateurController',
            'form' => $form->createView(),
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/user/{id}/task/{idTask}/add', name: 'app_user_task_add', options: ['expose' => true])]
    public function user_task_add(int $id, int $idTask, UtilisateurRepository $user, TacheRepository $tache, EntityManagerInterface $em, FlashMessageService $fm): Response {

        $u = $user->find($id);
        $t = $tache->find($idTask);


        if ($this->getUser() != $t->getPoste()->getFestival()->getOrganisateur() && !$t->getPoste()->getFestival()->getResponsables()->contains($this->getUser())) {
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


    #[Route('/user/poste/{idPoste}/adore', name: 'app_user_AdorePoste_add', options: ["expose" => true], methods: ['GET'])]
    public function user_AdorePoste_add(int $idPoste, UtilisateurRepository $user, PosteUtilisateurPreferencesRepository $posteUtilisateurPreferencesRepository, PosteRepository $posteRepository, EntityManagerInterface $em, FlashMessageService $fm): Response {

        $u = $this->getUser();
        if (!$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], Response::HTTP_FORBIDDEN);
        }

        $p = ($posteUtilisateurPreferencesRepository->findBy(["posteId"=>$idPoste,"UtilisateurId"=>$u]))[0];

        if (!$u) throw $this->createNotFoundException("L'utilisateur n'existe pas");
        if (!$p) throw $this->createNotFoundException("Le poste n'existe pas pour ce bénévole");

        if (!$p) {
            return new JsonResponse(['error' => "vous n'êtes pas bénévole pour le festival au quel appartiens ce post"], Response::HTTP_FORBIDDEN);
        }
        $p->setPreferencesDegree(1);

        $em->persist($p);
        $em->flush();

        return new JsonResponse(['success' => "mention j'adore ajoutée"], Response::HTTP_ACCEPTED);
    }

    #[Route('/user/poste/{idPoste}/liked', name: 'app_user_likedPoste_add', options: ["expose" => true], methods: ['GET'])]
    public function user_likedPoste_add(int $idPoste, PosteUtilisateurPreferencesRepository $posteUtilisateurPreferencesRepository, UtilisateurRepository $user, PosteRepository $posteRepository, EntityManagerInterface $em, FlashMessageService $fm): Response {

        $u = $this->getUser();
        if (!$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], Response::HTTP_FORBIDDEN);
        }

        $p = ($posteUtilisateurPreferencesRepository->findBy(["posteId"=>$idPoste,"UtilisateurId"=>$u]))[0];

        if (!$u) throw $this->createNotFoundException("L'utilisateur n'existe pas");
        if (!$p) throw $this->createNotFoundException("Le poste n'existe pas pour ce bénévole");

        if (!$p) {
            return new JsonResponse(['error' => "vous n'êtes pas bénévole pour le festival au quel appartiens ce post"], Response::HTTP_FORBIDDEN);
        }
        $p->setPreferencesDegree(0);

        $em->persist($p);
        $em->flush();
        return new JsonResponse(['success' => "mention j'aime ajoutée"], Response::HTTP_ACCEPTED);
    }

    #[Route('/user/poste/{idPoste}/disliked', name: 'app_user_likedPoste_remove', options: ["expose" => true], methods: ['GET'])]
    public function user_likedPoste_remove(int $idPoste, PosteUtilisateurPreferencesRepository $posteUtilisateurPreferencesRepository, UtilisateurRepository $user, PosteRepository $posteRepository, EntityManagerInterface $em, FlashMessageService $fm): Response {

        $u = $this->getUser();
        if (!$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], Response::HTTP_FORBIDDEN);
        }

        $p = ($posteUtilisateurPreferencesRepository->findBy(["posteId"=>$idPoste,"UtilisateurId"=>$u]))[0];

        if (!$u) throw $this->createNotFoundException("L'utilisateur n'existe pas");
        if (!$p) throw $this->createNotFoundException("Le poste n'existe pas pour ce bénévole");

        if (!$p) {
            return new JsonResponse(['error' => "vous n'êtes pas bénévole pour le festival au quel appartiens ce post"], Response::HTTP_FORBIDDEN);
        }
        $p->setPreferencesDegree(-1);

        $em->persist($p);
        $em->flush();
        return new JsonResponse(['success' => "mention j'aime annulée"], Response::HTTP_ACCEPTED);
    }

    #[Route('/user/{id}/task/{idTask}/remove', name: 'app_user_task_remove', options: ['expose' => true])]
    public function user_task_remove(int $id, int $idTask, UtilisateurRepository $user, TacheRepository $tache, EntityManagerInterface $em, FlashMessageService $fm): Response {

        $u = $user->find($id);
        $t = $tache->find($idTask);

        if ($this->getUser() != $t->getPoste()->getFestival()->getOrganisateur() && !$t->getPoste()->getFestival()->getResponsables()->contains($this->getUser())) {
            throw $this->createNotFoundException("Vous n'avez pas les droits pour supprimer un bénévole à cette tache");
        }

        if (!$u) throw $this->createNotFoundException("L'utilisateur n'existe pas");
        if (!$t) throw $this->createNotFoundException("La tache n'existe pas");

        $u->removeTache($t);
        $em->persist($u);
        $em->flush();

        $fm->add(FlashMessageType::SUCCESS, 'Tâche supprimée');

        return $this->redirectToRoute('home');
    }
}
