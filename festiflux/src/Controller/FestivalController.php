<?php

namespace App\Controller;

use App\Entity\Poste;

use App\Entity\PosteUtilisateurPreferences;
use App\Form\AjoutDebutFinType;
use App\Form\DemandeFestivalType;
use App\Form\ModifierFestivalType;
use App\Form\ModifierPosteType;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\FestivalRepository;
use App\Repository\TagRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tache;
use App\Form\ModifierTacheType;
use App\Entity\Utilisateur;
use App\Repository\DemandeFestivalRepository;
use App\Service\ErrorService;
use App\Service\FlashMessageService;
use App\Service\UtilisateurUtils;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Creneaux;
use App\Entity\Disponibilite;
use App\Entity\Festival;
use App\Entity\Lieu;
use App\Repository\DemandeBenevoleRepository;
use App\Repository\LieuRepository;
use App\Repository\PosteRepository;
use App\Repository\PosteUtilisateurPreferencesRepository;
use App\Repository\TacheRepository;
use DateTime;
use PHPUnit\Util\Json;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

use function PHPSTORM_META\map;

class FestivalController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(FestivalRepository $repository): Response
    {
        return $this->redirectToRoute('app_festival_all');
    }

    #[Route('/festival/all', name: 'app_festival_all')]
    public function all(FestivalRepository $repository, TagRepository $tagRepository, Request $request, FlashMessageService $flashMessageService): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $festivalsWithNameOrAddress = $repository->findBySearch($searchData);
            $listeTags = $tagRepository->findBySearch($searchData);
            $festivalsWithTag = [];
            foreach ($listeTags as $tag) {
                $festivals = (($tag)->getFestivals());
                foreach ($festivals as $f) {
                    $festivalsWithTag[] = $f;
                }
            }

            $allfest = $festivalsWithNameOrAddress;
            foreach ($festivalsWithTag as $fest) {
                if (!in_array($fest, $allfest)) {
                    $allfest[] = $fest;
                }
            }
            if ($allfest != null) {
                return $this->render('festival/index.html.twig', [
                    'form' => $form->createView(),
                    'searched' => true,
                    'festivals' => ($allfest)
                ]);
            } else {
                // $this->addFlash('error', 'Aucun festival ne correspond à votre recherche');
                return $this->render('festival/index.html.twig', [
                    'form' => $form->createView(),
                    'searched' => true,
                    'festivals' => []
                ]);
            }
        }

        $festivals = $repository->findAll();

        return $this->render('festival/index.html.twig', [
            'form' => $form->createView(),
            'festivals' => $festivals,
            'searched' => false
        ]);
    }

    #[Route('/festival/{id}/apply', name: 'app_festival_apply_volunteer')]
    public function apply(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils, EntityManagerInterface $em, FlashMessageService $flashMessageService, ErrorService $errorService, MailerInterface $mailer): Response
    {

        $festival = $repository->find($id);
        if (!$festival) {
            return $errorService->MustBeLoggedError();
        }

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', "Vous n'êtes pas inscrit");
            return $this->redirectToRoute('app_festival_detail', ['id' => $id]);
        }

        $isBenevole = $utilisateurUtils->isBenevole($u, $festival);
        $isResponsable = $utilisateurUtils->isResponsable($u, $festival);
        $isOrganisateur = $utilisateurUtils->isOrganisateur($u, $festival);

        if ($isBenevole || $isResponsable || $isOrganisateur) {
            $this->addFlash('error', 'Vous êtes déjà inscrit à ce festival');
            return $this->redirectToRoute('app_festival_detail', ['id' => $id]);
        }
        ;

        $festival->addDemandesBenevole($u);
        $em->persist($festival);
        $em->flush();

        $email = (new Email())
            ->from('administration@festiflux.fr')
            ->to($festival->getOrganisateur()->getEmail())
            ->subject('Demande de bénévolat')
            ->text('test')
            ->html('<p>Vous avez reçu une demande de bénévolat pour le festival ' . $festival->getNom() . '.' . ' <br><br> Cliquez <a href="http://127.0.0.1:8000/festival/' . $festival->getId() . '/demandes"  > ici </a> pour accéder aux demandes. </p>');

        $mailer->send($email);

        $this->addFlash('success', 'Demande de bénévolat envoyée');
        return $this->redirectToRoute('app_festival_detail', ['id' => $id]);
    }

    #[Route('/festival/{festId}/addResponsable/{userId}', name: 'app_festival_add_responsable', options: ["expose" => true])]
    public function addResponsabel(FestivalRepository $repository, UtilisateurRepository $userRepo, int $festId, int $userId, UtilisateurUtils $utilisateurUtils, EntityManagerInterface $em, ErrorService $errorService)
    {

        $festival = $repository->find($festId);
        $u = $userRepo->find($userId);

        if (!$festival) {
            return $errorService->MustBeLoggedError();
        }


        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', "cet utilisateur n'êtes pas inscrit");
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $festId]);
        }

        $isBenevole = $utilisateurUtils->isBenevole($u, $festival);
        $isResponsable = $utilisateurUtils->isResponsable($u, $festival);

        if (!$isBenevole) {
            $this->addFlash('error', "cet utilisateur n'êtes pas bénévole pour ce festival");
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $festId]);
        }
        ;
        if (!$isResponsable) {
            $festival->addResponsable($u);
            $em->persist($festival);
            $em->flush();
            $this->addFlash('success', 'Cet utilisateur est maintenant responsable pour ce festival');
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $festId]);
        } else {
            $this->addFlash('erreur', '');
            return $errorService->MustBeLoggedError();
        }
    }

    #[Route('/festival/{festId}/removeResponsable/{userId}', name: 'app_festival_remove_responsable', options: ["expose" => true])]
    public function removeResponsabel(FestivalRepository $repository, UtilisateurRepository $userRepo, int $festId, int $userId, UtilisateurUtils $utilisateurUtils, EntityManagerInterface $em, ErrorService $errorService)
    {

        $festival = $repository->find($festId);
        $u = $userRepo->find($userId);

        if (!$festival) {
            return $errorService->MustBeLoggedError();
        }


        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', "cet utilisateur n'êtes pas inscrit");
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $festId]);
        }

        $isBenevole = $utilisateurUtils->isBenevole($u, $festival);
        $isResponsable = $utilisateurUtils->isResponsable($u, $festival);

        if (!$isBenevole) {
            $this->addFlash('error', "cet utilisateur n'êtes pas bénévole pour ce festival");
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $festId]);
        }
        ;
        if ($isResponsable) {
            $festival->removeResponsable($u);
            $em->persist($festival);
            $em->flush();
            $this->addFlash('success', "Cet utilisateur n'est maintenant plus responsable pour ce festival");
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $festId]);
        } else {
            $this->addFlash('erreur', "");
            return $errorService->MustBeLoggedError();
        }
    }

    #[Route('/festival/{id}', name: 'app_festival_detail', options: ["expose" => true])]
    public function show(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils): Response
    {
        $festival = $repository->find($id);

        if (!$festival or $festival->getIsArchive() == 1) {
            $this->addFlash('error', 'Le festival n\'existe pas');
            return $this->redirectToRoute('home');
        }

        $isBenevole = false;
        $isResponsable = false;
        $isOrganisateur = false;
        $hasApplied = false;
        $u = $this->getUser();
        if ($u && $u instanceof Utilisateur) {
            $isBenevole = $utilisateurUtils->isBenevole($u, $festival);
            $isResponsable = $utilisateurUtils->isResponsable($u, $festival);
            $isOrganisateur = $utilisateurUtils->isOrganisateur($u, $festival);
            $hasApplied = $utilisateurUtils->hasApplied($u, $festival);
        }
        ;

        $postes = $festival->getPostes();

        return $this->render('festival/detailfest.html.twig', [
            'controller_name' => 'FestivalController',
            'festival' => $festival,
            'isBenevole' => $isBenevole,
            'isResponsable' => $isResponsable,
            'isOrganisateur' => $isOrganisateur,
            'hasApplied' => $hasApplied,
        ]);
    }

    #[Route('/festival/all/coordinate', name: 'app_all_festival', methods: ['GET'], options: ['expose' => true])]
    public function allCoordinatesFest(FestivalRepository $repository): JsonResponse
    {
        $festivals = $repository->findAll();
        $tab = [];
        foreach ($festivals as $festival) {
            $tab[] = [
                'id' => $festival->getId(),
                'nom' => $festival->getNom(),
                'latitude' => $festival->getLat(),
                'longitude' => $festival->getLon(),
            ];
        }
        return new JsonResponse($tab, 200);
    }

    #[Route('/festival/{id}/demandes', name: 'app_festival_demandesBenevolat')]
    public function showDemandes(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils): Response
    {
        $festival = $repository->find($id);
        if (!$festival) {
            throw $this->createNotFoundException("Le festival n'existe pas");
        }
        if (!$this->getUser() instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        $utilisateurUtils->isOrganisateur($this->getUser(), $festival);
        $benevoles = $festival->getBenevoles();
        $responsables = $festival->getResponsables();

        return $this->render('demandes_benevolat/demandesBenevole.html.twig', [
            'controller_name' => 'FestivalController',
            'demandes' => $festival->getDemandesBenevole(),
            'idFest' => $id,
            'benevoles' => $benevoles,
            'responsables' => $responsables,
        ]);
    }

    #[Route('/festival/{id}/planning', name: 'app_festival_planning')]
    public function planning(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils): Response
    {
        $festival = $repository->find($id);
        if (!$festival) {
            throw $this->createNotFoundException("Le festival n'existe pas");
        }

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        if (!($utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival) || $utilisateurUtils->isBenevole($u, $festival))) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette page');
            return $this->redirectToRoute('home');
        }

        return $this->render('festival/planning.html.twig', [
            'controller_name' => 'FestivalController',
            'festival' => $festival,
            'isOrgaOrResp' => $utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival),
            'userId' => $u->getId(),
        ]);
    }

    #[Route('/festival/{id}/postes', name: 'app_festival_postes')]
    public function postes(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils): Response
    {
        $festival = $repository->find($id);
        if (!$festival) {
            throw $this->createNotFoundException("Le festival n'existe pas");
        }

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        if (!($utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival) || $utilisateurUtils->isBenevole($u, $festival))) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette page');
            return $this->redirectToRoute('home');
        }

        return $this->render('festival/postes.html.twig', [
            'controller_name' => 'FestivalController',
            'festival' => $festival,
            'isOrgaOrResp' => $utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival),
        ]);
    }

    #[Route('/festival/{id}/demandes/accept/{idUser}', name: 'app_festival_accept_demande')]
    public function acceptDemandeBenevolat(int $id, int $idUser, FestivalRepository $repo, EntityManagerInterface $em)
    {

        $festival = $repo->find($id);
        $demande = $festival->getDemandesBenevole()->findFirst(function (int $_, Utilisateur $u) use ($idUser) {
            return $u->getId() == $idUser;
        });

        if (!$demande) {
            $this->addFlash('error', 'La demande n\'existe pas');
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $id]);
        }

        $festival->addBenevole($demande);
        $festival->removeDemandesBenevole($demande);
        $em->persist($festival);
        $em->flush();

        $this->addFlash('success', 'La demande a bien été acceptée');
        return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $id]);
    }

    #[Route('/festival/{id}/demandes/reject/{idUser}', name: 'app_festival_reject_demande')]
    public function rejectDemandeBenevolat(int $id, int $idUser, FestivalRepository $repo, EntityManagerInterface $em, DemandeBenevoleRepository $demandeRepo)
    {


        $festival = $repo->find($id);
        $demande = $festival->getDemandesBenevole()->findFirst(function (int $_, Utilisateur $u) use ($idUser) {
            return $u->getId() == $idUser;
        });

        if (!$demande) {
            $this->addFlash('error', 'La demande n\'existe pas');
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $id]);
        }

        $festival->removeDemandesBenevole($demande);
        $em->persist($festival);
        $em->flush();


        $this->addFlash('success', 'La demande a bien été rejetée');
        return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $id]);
    }

    #[Route('/festival/{id}/poste', name: 'app_festival_create_poste', methods: ['POST'], options: ["expose" => true])]
    public function createPoste(#[MapEntity] Festival $festival, Request $request, EntityManagerInterface $em, UtilisateurUtils $utilisateurUtils, PosteUtilisateurPreferencesRepository $posteUtilisateurPreferencesRepository): JsonResponse
    {
        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], 403);
        }

        if (!($utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival))) {
            return new JsonResponse(['error' => 'Vous n\'avez pas accès à cette page'], 403);
        }

        $poste = new Poste();
        $poste->setFestival($festival);
        $poste->setNom($request->toArray()['nom']);
        $poste->setCouleur($request->toArray()['couleur']);
        $poste->setDescription('');

        $em->persist($poste);
        $em->flush();

        return new JsonResponse([
            'success' => 'Le poste a bien été créé',
            'id' => $poste->getId(),
        ], 200);
    }

    #[Route('/festival/{id}/poste/all', name: 'app_festival_all_poste', methods: ['GET'], options: ["expose" => true])]
    public function allPoste(FestivalRepository $repository, #[MapEntity] Festival $festival, Request $request, EntityManagerInterface $em, UtilisateurUtils $utilisateurUtils): JsonResponse
    {
        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], 403);
        }
        
        $isBenevole = $utilisateurUtils->isBenevole($u, $festival);

        if (!($utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival)) && !$isBenevole) {
            return new JsonResponse(['error' => 'Vous n\'avez pas accès à cette page'], 403);
        }

        $postes = $festival->getPostes();

        $tab = [];
        foreach ($postes as $poste) {
            $tab[] = [
                'id' => $poste->getId(),
                'nom' => $poste->getNom(),
                'couleur' => $poste->getCouleur(),
                'description' => $poste->getDescription(),
            ];
        }

        return new JsonResponse([
            'postes' => $tab
        ], 200);
    }

    #[Route('/festival/{id}/poste/{idPoste}/edit', name: 'app_festival_edit_poste', methods: ['POST'], options: ['expose' => true])]
    public function editPoste(FestivalRepository $repository, Request $request, EntityManagerInterface $em, UtilisateurUtils $utilisateurUtils, int $id, int $idPoste, PosteRepository $poste) {

        $f = $repository->find($id);
        $p = $poste->find($idPoste);

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], 403);
        }

        $isBenevole = $utilisateurUtils->isBenevole($u, $f);

        if (!($utilisateurUtils->isOrganisateur($u, $f) || $utilisateurUtils->isResponsable($u, $f)) && !$isBenevole) {
            return new JsonResponse(['error' => 'Vous n\'avez pas accès à cette page'], 403);
        }

        if (!$f) {
            return new JsonResponse(['error' => 'Le festival n\'existe pas'], 403);
        }

        if (!$p) {
            return new JsonResponse(['error', 'Le poste n\'existe pas'], 403);
        }

        $p->setNom($request->toArray()['nom']);
        $p->setCouleur($request->toArray()['couleur']);
        $p->setDescription($request->toArray()['description']);

        $em->persist($p);
        $em->flush();

        return new JsonResponse(['success' => 'Le poste a bien été modifié'], Response::HTTP_OK);
    }



    #[Route('/festival/{id}/poste/{idPoste}/delete', name: 'app_festival_delete_poste', methods: ['GET', 'DELETE'], options: ["expose" => true])]
    public function deletePoste(Request $request, EntityManagerInterface $em, int $id, int $idPoste, FestivalRepository $repository, UtilisateurUtils $utilisateurUtils, PosteRepository $poste): Response
    {
        $f = $repository->find($id);
        $p = $poste->find($idPoste);

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], 403);
        }

        $isBenevole = $utilisateurUtils->isBenevole($u, $f);

        if (!($utilisateurUtils->isOrganisateur($u, $f) || $utilisateurUtils->isResponsable($u, $f)) && !$isBenevole) {
            return new JsonResponse(['error' => 'Vous n\'avez pas accès à cette page'], 403);
        }

        if (!$f) {
            return new JsonResponse(['error' => 'Le festival n\'existe pas'], 403);
        }

        if (!$p) {
            return new JsonResponse(['error', 'Le poste n\'existe pas'], 403);
        }

        $em->remove($p);
        $em->flush();

        return new JsonResponse(['success' => 'Le poste a bien été supprimée'], Response::HTTP_OK);
    }

    #[Route('/tache/{id}/affect', name: 'app_benevole_save', methods: ['POST'], options: ["expose" => true])]
    public function affectBenevoles(int $id, Request $request, EntityManagerInterface $em, UtilisateurUtils $utilisateurUtils, PosteRepository $poste, FestivalRepository $repository, TacheRepository $tacheRepository, UtilisateurRepository $ur): Response {
        $tache = $tacheRepository->find($id);
        $festival = $tache->getPoste()->getFestival();

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], 403);
        }

        if (!($utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival))) {
            return new JsonResponse(['error' => 'Vous n\'avez pas accès à cette page'], 403);
        }

        if (!$festival) {
            return new JsonResponse(['error' => 'Le festival n\'existe pas'], 403);
        }

        if (!$tache) {
            return new JsonResponse(['error', 'La tache n\'existe pas'], 403);
        }

        $affected = $request->toArray()['affected'];
        $unaffected = $request->toArray()['unaffected'];

        foreach ($affected as $benevole) {
            $ben = $ur->findBy(['id' => $benevole])[0];
            $tache->addBenevoleAffecte($ben);
        }

        foreach ($unaffected as $benevole) {
            $ben = $ur->findBy(['id' => $benevole])[0];
            $tache->removeBenevoleAffecte($ben);
        }

        $em->persist($tache);
        $em->flush();

        return new JsonResponse(['success' => 'Les bénévoles ont bien été affectés'], Response::HTTP_OK);
    }

    #[Route('/festival/{id}/benevole/all', name: 'app_festival_all_benevole',  methods: ['GET'], options: ['expose' => true])]
    public function allBenevoles(FestivalRepository $festRepo, PosteUtilisateurPreferencesRepository $prefRepo, #[MapEntity] Festival $festival, Request $request, EntityManagerInterface $em, UtilisateurUtils $utilisateurUtils): JsonResponse {

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], 403);
        }

        if (!($utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival) || $utilisateurUtils->isBenevole($u, $festival))) {
            return new JsonResponse(['error' => 'Vous n\'avez pas accès à cette page'], 403);
        }

        $benevoles = $festival->getBenevoles();
        $preferences = $prefRepo->findAll();

        $responseBenevoles = [];
        foreach ($benevoles as $benevole) {
            $responseBenevoles[] = [
                'id' => $benevole->getId(),
                'nom' => $benevole->getNom(),
                'prenom' => $benevole->getPrenom(),
                'preferences' => array(...array_map(function (PosteUtilisateurPreferences $pref) {
                    return [
                        'poste' => $pref->getPosteId()->getId(),
                        'degree' => $pref->getPreferencesDegree(),
                    ];
                }, array_filter($preferences, function (PosteUtilisateurPreferences $pref) use ($benevole) {
                    return $pref->getUtilisateurId()->getId() == $benevole->getId();
                }))),
                'indisponibilites' => array_map(function (Disponibilite $dispo) {
                    return [
                        'debut' => $dispo->getCreneau()->getDateDebut(),
                        'fin' => $dispo->getCreneau()->getDateFin(),
                    ];
                }, $benevole->getDisponibilites()->toArray()),
            ];
        }

        return new JsonResponse($responseBenevoles, 200);
    }

    #[Route('/tache/{id}/benevolespref', name: 'app_tache_benevolespref', methods: ['GET'], options: ['expose' => true])]
    public function Benevolespref(TacheRepository $repository, #[MapEntity] Tache $tache, Request $request, EntityManagerInterface $em, UtilisateurUtils $utilisateurUtils): JsonResponse
    {
        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], 403);
        }

        if (!($utilisateurUtils->isOrganisateur($u, $tache->getPoste()->getFestival()) || $utilisateurUtils->isResponsable($u, $tache->getPoste()->getFestival()))) {
            return new JsonResponse(['error' => 'Vous n\'avez pas accès à cette page'], 403);
        }

        $benevolesAdore = [];
        $benevolesAime = [];
        $benevolesAimePas = [];

        foreach ($tache->getPoste()->getPosteUtilisateurPreferences() as $bp) {
            if ($bp->getPreferencesDegree() == 1) {
                $benevolesAdore[] = $bp->getUtilisateurId();
            } else if ($bp->getPreferencesDegree() == 0) {
                $benevolesAime[] = $bp->getUtilisateurId();
            } else {
                $benevolesAimePas[] = $bp->getUtilisateurId();
            }
        }


        $tab1 = [];
        $tab2 = [];
        $tab3 = [];

        foreach ($benevolesAdore as $benevole) {
            $tab1[] = [
                'id' => $benevole->getId(),
                'nom' => $benevole->getNom(),
                'prenom' => $benevole->getPrenom(),
            ];
        }
        foreach ($benevolesAime as $benevole) {
            $tab2[] = [
                'id' => $benevole->getId(),
                'nom' => $benevole->getNom(),
                'prenom' => $benevole->getPrenom(),
            ];
        }
        foreach ($benevolesAimePas as $benevole) {
            $tab3[] = [
                'id' => $benevole->getId(),
                'nom' => $benevole->getNom(),
                'prenom' => $benevole->getPrenom(),
            ];
        }

        return new JsonResponse([
            'benevolesAdore' => $tab1,
            'benevolesAime' => $tab2,
            'benevolesAimePas' => $tab3,
        ], 200);
    }


    #[Route('/festival/{id}/DebutFinDay', name: 'app_festival_add_DebutFinDay', methods: ['POST'], options: ["expose" => true])]
    public function addDebutFinDay(#[MapEntity] Festival $f, Request $request, PosteRepository $posteRepository, EntityManagerInterface $em, UtilisateurUtils $utilisateurUtils): Response
    {
        if ($f == null) {
            return new JsonResponse(['error' => 'Le festival n\'existe pas'], Response::HTTP_NOT_FOUND);
        }
        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], Response::HTTP_FORBIDDEN);
        }
        if (!($utilisateurUtils->isOrganisateur($u, $f) || $utilisateurUtils->isResponsable($u, $f))) {
            return new JsonResponse(['error' => 'Vous ne pouvez pas effectuer cet opération'], Response::HTTP_FORBIDDEN);
        }
        $body = json_decode($request->getContent(), true);
        try {
            $dateDebut = new DateTime($body['debut']);
            $dateFin = new DateTime($body['fin']);

            if ($dateDebut > $dateFin) {
                return new JsonResponse(['error' => 'Les dates ne sont pas valides'], Response::HTTP_BAD_REQUEST);
            } else if ($dateDebut->format('Y-m-d') < $f->getDateDebut()->format('Y-m-d') || $dateDebut->format('Y-m-d') > $f->getDateFin()->format('Y-m-d')) {
                return new JsonResponse(['error' => 'La date de début n\'est pas valide'], Response::HTTP_BAD_REQUEST);
            } else if ($dateFin->format('Y-m-d') < $f->getDateDebut()->format('Y-m-d') || $dateFin->format('Y-m-d') > $f->getDateFin()->format('Y-m-d')) {
                return new JsonResponse(['error' => 'La date de fin n\'est pas valide'], Response::HTTP_BAD_REQUEST);
            }

            $c = new Creneaux();
            $c->setDateDebut($dateDebut);
            $c->setDateFin($dateFin);
            $f->addPlagesHoraire($c);

            $em->persist($c);
            $em->flush();
        } catch (\Throwable $th) {
            if ($th instanceof \ErrorException) {
                return new JsonResponse(['error' => "Les données ne sont pas valides"], Response::HTTP_BAD_REQUEST);
            }
            throw $th;
        }

        return new JsonResponse(status: Response::HTTP_CREATED);

    }

    #[Route('/festival/{id}/DebutFinDay', name: 'app_festival_get_DebutFinDay', methods: ['GET'], options: ["expose" => true])]
    public function getPlagesHoraires(#[MapEntity] Festival $f): JsonResponse
    {
        if ($f == null) {
            return new JsonResponse(['error' => 'Le festival n\'existe pas'], Response::HTTP_NOT_FOUND);
        }
        $creneaux = $f->getPlagesHoraires();
        $tab = [];
        foreach ($creneaux as $creneau) {
            $tab[] = [
                'id' => $creneau->getId(),
                'debut' => $creneau->getDateDebut(),
                'fin' => $creneau->getDateFin(),
            ];
        }
        return new JsonResponse($tab, 200);

    }

    #[Route('/festival/{id}/tache', name: 'app_festival_add_tache', methods: ['POST'], options: ["expose" => true])]
    public function addTache(#[MapEntity] Festival $f, Request $request, PosteRepository $posteRepository, EntityManagerInterface $em, int $id, UtilisateurUtils $utilisateurUtils): Response
    {


        if ($f == null) {
            return new JsonResponse(['error' => 'Le festival n\'existe pas'], Response::HTTP_NOT_FOUND);
        }

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], Response::HTTP_FORBIDDEN);
        }

        if (!($utilisateurUtils->isOrganisateur($u, $f) || $utilisateurUtils->isResponsable($u, $f))) {
            return new JsonResponse(['error' => 'Vous ne pouvez pas effectuer cet opération'], Response::HTTP_FORBIDDEN);
        }

        $body = json_decode($request->getContent(), true);


        try {
            $description = (string) $body['description'];
            $nombreBenevole = (int) $body['nombre_benevole'];
            $poste_id = (string) $body['poste_id'];
            $dateDebut = new DateTime($body['date_debut']);
            $dateFin = new DateTime($body['date_fin']);
            $lieu = (string) $body['lieu'];
            $address = (string) $body['adresse'];
        } catch (\Throwable $th) {
            if ($th instanceof \ErrorException) {
                return new JsonResponse(['error' => 'Les données ne sont pas valides'], Response::HTTP_BAD_REQUEST);
            }
            throw $th;
        }

        $p = $posteRepository->find($poste_id);


        if (!$p || $p->getFestival()->getId() != $id) {
            return new JsonResponse(['error' => 'Le poste n\'existe pas'], Response::HTTP_NOT_FOUND);
        }

        $t = new Tache();
        $t->setRemarque($description);
        $t->setNombreBenevole($nombreBenevole);

        if ($dateDebut > $dateFin) {
            return new JsonResponse(['error' => 'Les dates ne sont pas valides'], Response::HTTP_BAD_REQUEST);
        } else if ($dateDebut->format('Y-m-d') < $f->getDateDebut()->format('Y-m-d') || $dateDebut->format('Y-m-d') > $f->getDateFin()->format('Y-m-d')) {
            return new JsonResponse(['error' => 'La date de début n\'est pas valide'], Response::HTTP_BAD_REQUEST);
        } else if ($dateFin->format('Y-m-d') < $f->getDateDebut()->format('Y-m-d') || $dateFin->format('Y-m-d') > $f->getDateFin()->format('Y-m-d')) {
            return new JsonResponse(['error' => 'La date de fin n\'est pas valide'], Response::HTTP_BAD_REQUEST);
        }

        $c = new Creneaux();
        $c->setDateDebut($dateDebut);
        $c->setDateFin($dateFin);

        $l = new Lieu();
        $l->setNomLieu($lieu);
        $l->setAddress($address);
        $l->setFestival($f);

        $em->persist($l);
        $em->persist($c);

        $t->setCrenaux($c);
        $t->setPoste($p);
        $t->setLieu($l);

        $em->persist($t);

        $em->flush();

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    #[Route('/festival/{id}/tache', name: 'app_festival_tache', methods: ['GET'], options: ["expose" => true])]
    public function getTaches(#[MapEntity] Festival $f): JsonResponse
    {


        if ($f == null) {
            return new JsonResponse(['error' => 'Le festival n\'existe pas'], Response::HTTP_NOT_FOUND);
        }

        $taches = $f->getPostes()->reduce(function ($acc, Poste $p) {
            return array_merge($acc, array_map(function (Tache $el) use ($p) {
                return [
                    'date_debut' => $el->getCrenaux()->getDateDebut(),
                    'date_fin' => $el->getCrenaux()->getDateFin(),
                    'poste_id' => $p->getId(),
                    'poste_nom' => $p->getNom(),
                    'poste_couleur' => $p->getCouleur(),
                    'poste_description' => $p->getDescription(),
                    'lieu' => $el->getLieu()->getNomLieu(),
                    'description' => $el->getRemarque(),
                    'nombre_benevole' => $el->getNombreBenevole(),
                    'benevole_affecte' => $el->getBenevoleAffecte()->count(),
                    'id' => $el->getId(),
                    'benevoles' => array_map(function (Utilisateur $u) use ($el) {
                        return $u->getId();
                    }, $el->getBenevoleAffecte()->toArray())
                ];
            }, $p->getTaches()->toArray()));
        }, []);


        //dd($taches);
        return new JsonResponse($taches, Response::HTTP_OK);
    }

    #[Route('/festival/{id}/tache/{idTache}/edit', name: 'app_festival_edit_tache', methods: ['GET', 'POST'], options: ["expose" => true])]
    public function editTache(Request $request, EntityManagerInterface $em, int $id, int $idTache, FestivalRepository $fRep, TacheRepository $tRep, PosteRepository $pRep, LieuRepository $lRep, Request $req): Response
    {

        $f = $fRep->find($id);
        $t = $tRep->find($idTache);

        if (!$f) {
            throw $this->createNotFoundException("Le festival n'existe pas");
        }

        if (!$t) {
            throw $this->createNotFoundException("La tache n'existe pas");
        }

        $body = json_decode($request->getContent(), true);

        try {
            $description = (string) $body['description'];
            $nombreBenevole = (int) $body['nombre_benevole'];
            $poste_id = (string) $body['poste_id'];
            $dateDebut = new DateTime($body['date_debut']);
            $dateFin = new DateTime($body['date_fin']);
            $nomLieu = (string) $body['lieu'];
            $address = (string) $body['adresse'];
        } catch (\Throwable $th) {
            if ($th instanceof \ErrorException) {
                return new JsonResponse(['error' => 'Les données ne sont pas valides'], Response::HTTP_BAD_REQUEST);
            }

            throw $th;
        }

        $lieu = $lRep->findBy(['nomLieu' => $nomLieu, 'festival' => $f]);
        if (!$lieu) {
            $lieu = new Lieu();
            $lieu->setNomLieu($nomLieu);
            $lieu->setAddress($address);
            $lieu->setFestival($f);
            $em->persist($lieu);
        }

        $t->setRemarque($description);
        $t->setNombreBenevole($nombreBenevole);
        $t->setPoste($pRep->find($poste_id));
        $t->getCrenaux()->setDateDebut($dateDebut);
        $t->getCrenaux()->setDateFin($dateFin);
        $t->setLieu($lieu);
        $t->getLieu()->setAddress($address);

        $em->persist($t);
        $em->flush();

        $this->addFlash('success', 'La tache a bien été modifiée');
        return $this->redirectToRoute('app_festival_planning', ['id' => $id]);


        //a modifier
        return $this->render('festival/testmodiftache.html.twig', [
            'controller_name' => 'FestivalController',


        ]);
    }

    #[Route('/festival/{id}/tache/{idTache}/delete', name: 'app_festival_delete_tache', methods: ['DELETE'], options: ["expose" => true])]
    public function deleteTache(Request $request, EntityManagerInterface $em, int $id, int $idTache, FestivalRepository $fRep, TacheRepository $tRep): Response
    {

        $f = $fRep->find($id);
        $t = $tRep->find($idTache);

        if (!$f) {
            throw $this->createNotFoundException("Le festival n'existe pas");
        }

        if (!$t) {
            throw $this->createNotFoundException("La tache n'existe pas");
        }

        $em->remove($t);
        $em->flush();

        return new JsonResponse(['success' => 'La tache a bien été supprimée'], Response::HTTP_OK);
    }


    #[Route('/festival/{id}/modifier', name: 'app_festival_modifier')]
    public function edit(#[MapEntity] Festival $festival, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {

        if (!$festival) {
            throw $this->createNotFoundException('Festival non trouvé.');
        }


        $form = $this->createForm(ModifierFestivalType::class, $festival);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $affiche = $form->get('affiche')->getData();

            if ($affiche instanceof UploadedFile) {
                $originalFilename = pathinfo("", PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $affiche->guessExtension();

                $affiche->move(
                    $this->getParameter('poster_directory'),
                    $newFilename
                );

                $festival->setAffiche($newFilename);
            }

            $em->flush();
            $this->addFlash('success', 'Le festival a été modifié avec succès.');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        }

        return $this->render('festival/modifier.html.twig', [
            'controller_name' => 'FestivalController',
            'form' => $form->createView(),
            'festival' => $festival,
        ]);
    }

    #[Route('/festival/{id}/archiver', name: 'app_festival_archiver')]
    public function demandeArchiverFestival(FestivalRepository $repository, int $id): Response
    {
        $festival = $repository->find($id);

        if (!$festival) {
            $this->addFlash('error', 'Le festival n\'existe pas');
            return $this->redirectToRoute('home');
        }

        return $this->render('festival/archive.html.twig', [
            'controller_name' => 'FestivalController',
            'festival' => $festival
        ]);
    }

    #[Route('/festival/{id}/archiver/done', name: 'app_festival_archiver_done')]
    public function archiverFestival(FestivalRepository $repository, int $id, EntityManagerInterface $em): Response
    {
        $festival = $repository->find($id);

        if (!$festival) {
            $this->addFlash('error', 'Le festival n\'existe pas');
            return $this->redirectToRoute('home');
        }

        $festival->setIsArchive();

        $em->persist($festival);
        $em->flush();

        return $this->redirectToRoute('app_user_festivals');
    }

    #[Route('/festival/{id}/postesPref', name: 'app_festival_display_postes')]
    public function displayPostes(#[MapEntity] Festival $festival, PosteUtilisateurPreferencesRepository $posteUtilisateurPreferencesRepository, PosteRepository $posteRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {

        if (!$festival) {
            throw $this->createNotFoundException('Festival non trouvé.');
        }
        $postes = $posteRepository->findBy(["festival" => $festival]);
        $u = $this->getUser();

        $pref = $posteUtilisateurPreferencesRepository->findBy(["UtilisateurId" => $u]);

        return $this->render('utilisateur/liked_postes.html.twig', [
            'postes' => $postes,
            'utilisateur' => $u,
            'preferences' => $pref
        ]);
    }

    
    #[Route('/festival/{id}/gestion', name: 'app_festival_gestion')]
    public function gestion(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils): Response
    {

        $festival = $repository->find($id);
        if (!$festival) {
            throw $this->createNotFoundException('Festival non trouvé.');
        }      

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }


        return $this->render('festival/gestionFest.html.twig', [
            'controller_name' => 'FestivalController',
            'festival' => $festival,
            'isOrgaOrResp' => $utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival),
            'userId' => $u->getId(),
        ]);

    }
}
