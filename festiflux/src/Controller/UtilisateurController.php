<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Creneaux;
use App\Entity\Disponibilite;
use App\Entity\Festival;
use App\Entity\Poste;
use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use App\Form\ModifierFestivalType;
use App\Form\ModifierProfilType;
use App\Repository\FestivalRepository;
use App\Repository\HistoriquePostulationRepository;
use App\Repository\PosteRepository;
use App\Repository\PosteUtilisateurPreferencesRepository;
use App\Entity\PosteUtilisateurPreferences;
use App\Repository\ContactRepository;
use App\Repository\TacheRepository;
use App\Repository\TypeContactRepository;
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
use App\Service\UtilisateurManager;
use Symfony\Component\Validator\Constraints\Date;
use App\Service\UtilisateurManagerInterface;
use PHPUnit\Util\Json;
use DateTime;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UtilisateurController extends AbstractController {

    public function __construct(
        public FlashMessageService $flashMessageService, 
        public UtilisateurManager $utilisateurManager,
        public EntityManagerInterface $em
        ) {}


    #[Route('/user/info', name: 'app_user_info', methods: ['GET'])]
    public function info(UtilisateurRepository $repository, #[CurrentUser] Utilisateur $utilisateur, Request $request, EntityManagerInterface $em, UtilisateurManagerInterface $utilisateurManager): Response {
        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
        $form = $this->createForm(ModifierProfilType::class, $utilisateur);
        $form->handleRequest($request);

        return $this->render('utilisateur/modifierProfil.html.twig', [
            'controller_name' => 'UtilisateurController',
            'form' => $form->createView(),
            'utilisateur' => $utilisateur,
        ]);
        
    }

    #[Route('/user/{id}/profile', name: 'app_user_profile')]
    public function profile(int $id, UtilisateurRepository $utilisateurRepository): Response {

        $u = $utilisateurRepository->find($id);
        if (!$u)
            throw $this->createNotFoundException("L'utilisateur n'existe pas");

        return $this->render('utilisateur/public_profile.html.twig', [
            'controller_name' => 'UtilisateurController',
            'user' => $u
        ]);
        
    }

    #[Route('/user/festivals', name: 'app_user_festivals', methods: ['GET'])]
    public function userFestivals(Request $request, FestivalRepository $festivalRepository): Response {

        $filter = $request->query->get('filter');

        if ($filter !== 'volunteer' && $filter !== 'owned') {
            return $this->redirectToRoute('app_user_festivals', ['filter' => 'volunteer']);
        }


        $u = $this->getUser();

        if (!$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }


        $festivals = [];
        if ($filter === 'volunteer') {
            $festivals = $u->getEstBenevole();
        } else {
            $festivals = $festivalRepository->findBy([
                'organisateur' => $u->getId()
            ]);
        }

        return $this->render('utilisateur/user_festivals.html.twig', [
            'controller_name' => 'UtilisateurController',
            'festivals' => $festivals,
            'filter' => $filter,
        ]);
    }


    #[Route('/icalLink/{idFest}', name: 'app_icalLink', methods: ['GET'], options: ['expose' => true])]
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
                $ical->add(
                    new Event(
                        "Event" . $t->getId(),
                        $t->getPoste()->getNom(),
                        $t->getRemarque(),
                        $t->getCrenaux()->getDateDebut(),
                        $t->getCrenaux()->getDateFin(),
                        $t->getLieu()->getNomLieu(),
                        null
                    )
                );
            }
            $ical->build();
            return $this->file('icals/' . $ical->getFilename() . '.ics');
        }

        if ($utilisateurUtils->isResponsable($currentUser, $fest) || $utilisateurUtils->isOrganisateur($currentUser, $fest)) {
            $posts = $posteRepository->findBy(["festival" => $fest]);
            $ical = new IcalBuilder('ResponsableCaledar');
            foreach ($posts as $p) {
                $taches = $p->getTaches();
                foreach ($taches as $t) {
                    if ($t->getPoste()->getFestival() === $fest) {
                        $ical->add(
                            new Event(
                                "Event" . $t->getId(),
                                $t->getPoste()->getNom(),
                                $t->getRemarque(),
                                $t->getCrenaux()->getDateDebut(),
                                $t->getCrenaux()->getDateFin(),
                                $t->getLieu()->getNomLieu(),
                                $t->getNombreBenevole()
                            )
                        );
                    }
                }
            }
            $ical->build();
            return $this->file('icals/' . $ical->getFilename() . '.ics');
        }
    }

    #[Route('/user/profile/{id}/edit', name: 'app_profile_edit', methods: ['POST'])]
    public function edit(UtilisateurRepository $repository, #[MapEntity] Utilisateur $utilisateur, Request $request, EntityManagerInterface $em, UtilisateurManagerInterface $utilisateurManager): Response {

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
        $form = $this->createForm(ModifierProfilType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoProfil = $form["fichierPhotoProfil"]->getData();
            $utilisateurManager->processNewUtilisateur($utilisateur, $photoProfil);
            $em->flush();
            $this->addFlash('success', 'Votre profil a été modifié avec succès.');
            return $this->redirectToRoute('app_profile_edit', ['id' => $utilisateur->getId()]);
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
            throw $this->createNotFoundException("Vous n'avez pas les droits pour ajouter un bénévole à cette tâche");
        }

        if (!$u)
            throw $this->createNotFoundException("L'utilisateur n'existe pas");
        if (!$t)
            throw $this->createNotFoundException("La tâche n'existe pas");

        // TODO : faire une vérification sur les dispo / pref de l'utilisateur

        $u->addTache($t);
        $em->persist($u);
        $em->flush();

        $fm->add(FlashMessageType::SUCCESS, 'Tâche ajoutée');

        return $this->redirectToRoute('home');
    }


    #[Route('/user/poste/{id}/pref', name: 'app_user_add_pref_poste', options: ["expose" => true], methods: ['POST'])]
    public function user_AdorePoste_add(#[MapEntity] Poste $poste, Request $request, UtilisateurRepository $user, PosteUtilisateurPreferencesRepository $posteUtilisateurPreferencesRepository, PosteRepository $posteRepository, EntityManagerInterface $em, UtilisateurUtils $uu): Response {

        $u = $this->getUser();

        if (!$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], Response::HTTP_FORBIDDEN);
        }

        $festival = $poste->getFestival();
        $uu->isBenevole($u, $festival);

        $prefs = ($posteUtilisateurPreferencesRepository->findBy(["posteId" => $poste, "UtilisateurId" => $u]));

        $pref = null;

        if (count($prefs) > 0) {
            $pref = $prefs[0];
        } else {
            $pref = new PosteUtilisateurPreferences();
            $pref->setPosteId($poste);
            $pref->setUtilisateurId($u);
        }

        $degree = $request->toArray()['degree'];

        if ($degree !== 0 && $degree !== 1 && $degree !== -1) {
            return new JsonResponse(['error' => 'degré de préférence non fourni'], Response::HTTP_BAD_REQUEST);
        }

        $pref->setPreferencesDegree($degree);

        $em->persist($pref);
        $em->flush();

        return new JsonResponse(['success' => "Mention ajoutée!"], Response::HTTP_ACCEPTED);
    }

    #[Route('/user/{id}/task/{idTask}/remove', name: 'app_user_task_remove', options: ['expose' => true])]
    public function user_task_remove(int $id, int $idTask, UtilisateurRepository $user, TacheRepository $tache, EntityManagerInterface $em, FlashMessageService $fm): Response {

        $u = $user->find($id);
        $t = $tache->find($idTask);

        if ($this->getUser() != $t->getPoste()->getFestival()->getOrganisateur() && !$t->getPoste()->getFestival()->getResponsables()->contains($this->getUser())) {
            throw $this->createNotFoundException("Vous n'avez pas les droits pour supprimer un bénévole à cette tâche");
        }

        if (!$u)
            throw $this->createNotFoundException("L'utilisateur n'existe pas");
        if (!$t)
            throw $this->createNotFoundException("La tâche n'existe pas");

        $u->removeTache($t);
        $em->persist($u);
        $em->flush();

        $fm->add(FlashMessageType::SUCCESS, 'Tâche supprimée');

        return $this->redirectToRoute('home');
    }

    #[Route('/festival/{id}/disponibilities', name: 'app_festival_add_disponibilities', options: ['expose' => true], methods: ['POST'])]
    public function addDispo(#[MapEntity] Festival $festival, Request $request, EntityManagerInterface $em, UtilisateurUtils $user): Response {
        $u = $this->getUser();
        $f = $festival->getId();

        if (!$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], Response::HTTP_FORBIDDEN);
        }

        $isBenevole = $user->isBenevole($u, $festival);

        if (!$isBenevole) {
            return new JsonResponse(['error' => 'Vous n\'avez pas accès à cette page'], 403);
        }

        if (!$f) {
            return new JsonResponse(['error' => 'Le festival n\'existe pas'], 403);
        }

        $data = json_decode($request->getContent(), true);

        try {
            $dateDebut = new DateTime($data['debut']);
            $dateFin = new DateTime($data['fin']);

            if ($dateDebut >= $dateFin) {
                return new JsonResponse(['error' => 'Les heures fournies sont invalides'], Response::HTTP_BAD_REQUEST);
            }

            if ($dateDebut < $festival->getDateDebut() || $dateFin > $festival->getDateFin()) {
                return new JsonResponse(['error' => 'Les dates fournies dépassent la plage horaire du festival'], Response::HTTP_BAD_REQUEST);
            }

            $c = new Creneaux();
            $c->setDateDebut($dateDebut);
            $c->setDateFin($dateFin);
            $em->persist($c);

            $dispo = new Disponibilite();
            $dispo->setUtilisateur($u);
            $dispo->setFestival($festival);
            $dispo->setCreneau($c);

            $em->persist($dispo);
            $em->flush();

            return new JsonResponse(status: Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            if ($th instanceof \ErrorException) {
                return new JsonResponse(['error' => 'Les données ne sont pas valides'], Response::HTTP_BAD_REQUEST);
            }
            throw $th;
        }
    }

    #[Route('/festival/{id}/preferences/{userId}', name: 'app_festival_get_preferences', options: ['expose' => true], methods: ['GET'])]
    public function getPreferences(#[MapEntity] Festival $festival, #[MapEntity(id: 'userId')] Utilisateur $user, Request $request, EntityManagerInterface $em, UtilisateurUtils $uu): Response {
        $u = $this->getUser();
        $f = $festival->getId();

        if (!$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], Response::HTTP_FORBIDDEN);
        }
        
        $isOrgaOrResp = $uu->isOrganisateur($u, $festival) || $uu->isResponsable($u, $festival);
    
        if (!$isOrgaOrResp && $u->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Vous n\'avez pas accès à cette page'], 403);
        }

        if (!$f) {
            return new JsonResponse(['error' => 'Le festival n\'existe pas'], 403);
        }

        $prefs = $user->getPosteUtilisateurPreferences()->filter(function ($p) use ($festival) {
            return $p->getPosteId()->getFestival() === $festival;
        });

        $prefs = array_map(function ($p) {
            return [
                'poste' => $p->getPosteId()->getId(),
                'degree' => $p->getPreferencesDegree(),
            ];
        }, $prefs->toArray());

        return new JsonResponse(array(...$prefs), Response::HTTP_OK);
    }

    #[Route('/user/{id}/trakingBenevoleRequest', name: 'app_user_traking_benevole_request')]
    public function trakingBenevoleRequest(#[MapEntity] Utilisateur $utilisateur,  FlashMessageService $flashMessageService, HistoriquePostulationRepository $historiquePostulationRepository, Request $request, EntityManagerInterface $em, UtilisateurUtils $user): Response {
        if (!$utilisateur instanceof Utilisateur) {
            $flashMessageService->add(FlashMessageType::ERROR, "L'utilisateur n'existe pas");
            return $this->redirectToRoute('home');
        } else {
            $demandePostulation = $historiquePostulationRepository->findBy(['utilisateur' => $utilisateur->getId()]);
            return $this->render('utilisateur/trakingBenevoleRequest.html.twig', [
                'controller_name' => 'UtilisateurController',
                'demandes' => $demandePostulation,
            ]);
        }
    }

    #[Route('/user/name/{id}', name: 'app_user_name', methods: ['GET'], options: ['expose' => true])]
    public function name(int $id, UtilisateurRepository $utilisateurRepository): Response {
        $u = $utilisateurRepository->find($id);
        if (!$u)
            throw $this->createNotFoundException("L'utilisateur n'existe pas");
        return new JsonResponse(['name' => $u->getNom() . ' ' . $u->getPrenom()]);
    }


}
