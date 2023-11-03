<?php

namespace App\Controller;

use App\Entity\Festival;
use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use App\Form\ModifierFestivalType;
use App\Form\ModifierProfilType;
use App\Repository\FestivalRepository;
use App\Repository\PosteRepository;
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

    #[Route('/testical', name: 'app_testical', methods: ['GET'])]
    public function testical(): Response {
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
        if ($utilisateurUtils->isBenevole($currentUser,$fest)){
            $taches = $currentUser->getTaches();
            $ical = new IcalBuilder('benevoleCalendar');
            foreach ($taches as $t){
                $ical->add(new Event(
                    "Event".$t->getId(),
                    $t->getPoste()->getNom(),
                    $t->getDescription(),
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
                ->attach(fopen('icals/'.$ical->getFilename().'.ics', 'r'),$ical->getFilename().'.ics')
                ->html('<p>Voici votre fichier ics des taches qui vous sont assignées en tant que bénévole pour le festival ' . $fest->getNom() . '.' . ' <br><br> Cliquez <a href="https://www.frandroid.com/comment-faire/tutoriaux/tutoriels-pc/1558958_comment-ajouter-un-evenement-icalendar-ics-a-google-agenda"  > ici </a> pour avoir un tuto pas à pas sur "Comment ajouter un. ics à Google Agenda". </p>');

            $mailer->send($email);

        }
        if ($utilisateurUtils->isResponsable($currentUser,$fest) || $utilisateurUtils->isOrganisateur($currentUser,$fest) ){
            $posts=$posteRepository->findBy(["festival"=>$fest]);
            $ical = new IcalBuilder('ResponsableCaledar');
            foreach ($posts as $p){
                $taches = $p->getTaches();
                foreach ($taches as $t){
                    if($t->getPoste()->getFestival()===$fest) {
                        $ical->add(new Event(
                            "Event" . $t->getId(),
                            $t->getPoste()->getNom(),
                            $t->getDescription(),
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
                ->attach(fopen('icals/'.$ical->getFilename().'.ics', 'r'),$ical->getFilename().'.ics')
                ->html('<p>Voici votre fichier ics des taches pour le festival ' . $fest->getNom() . '.' . ' <br><br> Cliquez <a href="https://www.frandroid.com/comment-faire/tutoriaux/tutoriels-pc/1558958_comment-ajouter-un-evenement-icalendar-ics-a-google-agenda"  > ici </a> pour avoir un tuto pas à pas sur "Comment ajouter un. ics à Google Agenda". </p>');

            $mailer->send($email);
        }



        return $this->redirectToRoute('app_festival_all');
    }
  
    #[Route('/user/profile/{id}/edit', name: 'app_profile_edit')]
    public function edit(UtilisateurRepository $repository, #[MapEntity] Utilisateur $utilisateur, Request $request, EntityManagerInterface $em, ): Response {

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }


        $form = $this->createForm(ModifierProfilType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


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


    #[Route('/user/poste/{idPoste}/liked', name: 'app_user_likedPoste_add', options: ["expose" => true], methods: ['GET'])]
    public function user_likedPoste_add(int $idPoste, UtilisateurRepository $user, PosteRepository $posteRepository, EntityManagerInterface $em, FlashMessageService $fm): Response {

        $u = $this->getUser();
        if (!$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], Response::HTTP_FORBIDDEN);
        }

        $p = $posteRepository->find($idPoste);

        if (!$u) throw $this->createNotFoundException("L'utilisateur n'existe pas");
        if (!$p) throw $this->createNotFoundException("La tache n'existe pas");

        if (!$u->getEstBenevole()->contains($p->getFestival())){
            return new JsonResponse(['error' => "vous n'êtes pas bénévole pour le festival au quel appartiens ce post"], Response::HTTP_FORBIDDEN);
        }
        $p->addUtilisateursAime($u);

        $em->persist($p);
        $em->flush();

        return new JsonResponse(['success' => "mention j'aime ajoutée"], Response::HTTP_ACCEPTED);


    }

    #[Route('/user/poste/{idPoste}/disliked', name: 'app_user_likedPoste_remove', options: ["expose" => true], methods: ['GET'])]
    public function user_likedPoste_remove(int $idPoste, UtilisateurRepository $user, PosteRepository $posteRepository, EntityManagerInterface $em, FlashMessageService $fm): Response {

        $u = $this->getUser();
        if (!$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], Response::HTTP_FORBIDDEN);
        }

        $p = $posteRepository->find($idPoste);

        if (!$u) throw $this->createNotFoundException("L'utilisateur n'existe pas");
        if (!$p) throw $this->createNotFoundException("La tache n'existe pas");

        if (!$u->getEstBenevole()->contains($p->getFestival())){
            return new JsonResponse(['error' => "vous n'êtes pas bénévole pour le festival au quel appartiens ce post"], Response::HTTP_FORBIDDEN);
        }

        $p->removeUtilisateursAime($u);
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
