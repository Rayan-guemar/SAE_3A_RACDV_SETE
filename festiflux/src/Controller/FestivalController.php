<?php

namespace App\Controller;

use App\Entity\HistoriquePostulation;
use App\Entity\Poste;

use App\Entity\PosteUtilisateurPreferences;
use App\Entity\QuestionBenevole;
use App\Entity\Tag;
use App\Entity\Validation;
use App\Form\FestivalType;
use App\Form\ModifierFestivalType;
use App\Form\ModifierQuestionBenevoleType;
use App\Form\QuestionBenevoleType;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\FestivalRepository;
use App\Repository\QuestionBenevoleRepository;
use App\Repository\HistoriquePostulationRepository;
use App\Repository\TagRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\ValidationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tache;
use App\Entity\Utilisateur;
use App\Service\ErrorService;
use App\Service\FlashMessageService;
use App\Service\UtilisateurUtils;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Creneaux;
use App\Entity\Disponibilite;
use App\Entity\Festival;
use App\Entity\Lieu;
use App\Entity\Plage;
use App\Repository\LieuRepository;
use App\Repository\PosteRepository;
use App\Repository\PosteUtilisateurPreferencesRepository;
use App\Repository\TacheRepository;
use DateTime;
use Doctrine\DBAL\Query;
use PHPUnit\Util\Json;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;


#[Route('{_locale<%app.supported_locales%>}')]
class FestivalController extends AbstractController {
    public function __construct(
        public FlashMessageService $flashMessageService, 
        public UtilisateurRepository $utilisateurRepository, 
        public EntityManagerInterface $em
    ) {}

    #[Route('/', name: 'home')]
    public function index(FestivalRepository $repository): Response {
        return $this->redirectToRoute('app_festival_all');
    }

    /**
     * pour afficher les festivals sur la page d'acceuil
     */
    #[Route('/festival/all', name: 'app_festival_all')]
    public function all(FestivalRepository $repository, TagRepository $tagRepository, Request $request, FlashMessageService $flashMessageService, PaginatorInterface $paginator): Response {
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

        //get les festivals qui ne sont pas archivés
        $festivals = $repository->findBy(['isArchive' => 0, 'open' => 1]);
        $cards = $paginator->paginate(
            $festivals,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('festival/index.html.twig', [
            'form' => $form->createView(),
            'festivals' => $cards,
            'searched' => false
        ]);
    }

    /**
     * pour ajouter un festival
     */
    #[Route('/festival/add', name: 'app_festival_add', methods: ['GET', 'POST'])]
    public function add(TagRepository $tagRepository, Request $req, EntityManagerInterface $em, SluggerInterface $slugger, FlashMessageService $flashMessageService, TranslatorInterface $translator): Response {
        $festivals = new Festival();
        $form = $this->createForm(FestivalType::class, $festivals);
        $form->handleRequest($req);
        if ($req->isMethod('POST')) {
            $this->denyAccessUnlessGranted('ROLE_USER');
            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->get('dateFin')->getData() > $form->get('dateDebut')->getData()) {
                    $affiche = $form->get('affiche')->getData();
                    if ($affiche) {
                        $originalFilename = pathinfo("", PATHINFO_FILENAME);
                        $safeFilename = $slugger->slug($originalFilename);
                        $newFilename = $safeFilename . '-' . uniqid() . '.' . $affiche->guessExtension();
                        try {
                            $affiche->move(
                                $this->getParameter('poster_directory'),
                                $newFilename
                            );
                        } catch (FileException $e) {

                            throw new \Exception($translator->trans('festival.error.upload', [], 'msgflash', $translator->getLocale()));
                        }
                        $festivals->setAffiche($newFilename);
                    }

                    $festivals->setNom($form->get('nom')->getData());
                    $festivals->setDateDebut($form->get('dateDebut')->getData());
                    $festivals->setDateFin($form->get('dateFin')->getData());
                    $festivals->setDescription($form->get('description')->getData());
                    $festivals->setOrganisateur($this->getUser());
                    $festivals->setLieu($form->get('lieu')->getData());
                    $festivals->setLat($form->get('lat')->getData());
                    $festivals->setLon($form->get('lon')->getData());

                    $tag_arr = explode(",", $form->get('tags')->getData());
                    foreach ($tag_arr as $tagName) {
                        $verif = ($tagRepository)->findBy(["nom" => $tagName]);
                        ($verif != null) ? $tag = $verif[0] : $tag = new Tag($tagName);
                        $em->persist($tag);
                        $festivals->addTag($tag);
                    }

                    $em->persist($festivals);
                    $em->flush();

                    $this->addFlash('success', $translator->trans('festival.success.demande', [], 'msgflash', $translator->getLocale()));
                    return $this->redirectToRoute('app_festival_gestion', ['id' => $festivals->getId()]);
                } else {
                    $this->addFlash('error', $translator->trans('festival.error.date', [], 'msgflash', $translator->getLocale()));
                    return $this->redirectToRoute('app_festival_add');
                }
            } else {
                $flashMessageService->addErrorsForm($form);
                return $this->redirectToRoute('app_festival_add');
            }
        }
        return $this->render('demande_festival/add.html.twig', [
            'controller_name' => 'FestivalController',
            'form' => $form->createView()
        ]);
    }

    /**
     * pour postuler à un festival {id}
     */
    #[Route('/festival/{id}/apply', name: 'app_festival_apply_volunteer')]
    public function apply(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils, EntityManagerInterface $em, TranslatorInterface $translator, ErrorService $errorService, MailerInterface $mailer): Response {

        $festival = $repository->find($id);
        if (!$festival) {
            return $errorService->MustBeLoggedError();
        }

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_detail', ['id' => $id]);
        }

        $isBenevole = $utilisateurUtils->isBenevole($u, $festival);
        $isResponsable = $utilisateurUtils->isResponsable($u, $festival);
        $isOrganisateur = $utilisateurUtils->isOrganisateur($u, $festival);

        if ($isBenevole || $isResponsable || $isOrganisateur) {
            $this->addFlash('error', $translator->trans('user.error.alreadyMember', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_detail', ['id' => $id]);
        };

        $festival->addDemandesBenevole($u);
        $historiquePostulation = new HistoriquePostulation();
        $historiquePostulation->setFestival($festival);
        $historiquePostulation->setIdUtilisateur($u);
        $historiquePostulation->setStatut(0);
        $historiquePostulation->setDateDemande(new DateTime());
        $em->persist($historiquePostulation);
        $festival->addHistoriquePostulation($historiquePostulation);

        $em->persist($festival);
        $em->flush();

        $email = (new Email())
            ->from('administration@festiflux.fr')
            ->to($festival->getOrganisateur()->getEmail())
            ->subject('Demande de bénévolat')
            ->text('test')
            ->html('<p>Vous avez reçu une demande de bénévolat pour le festival ' . $festival->getNom() . '.' . ' <br><br> Cliquez <a href="http://127.0.0.1:8000/festival/' . $festival->getId() . '/demandes"  > ici </a> pour accéder aux demandes. </p>');

        $mailer->send($email);

        $this->addFlash('success', $translator->trans('festival.success.volunRequestSent', [], 'msgflash', $translator->getLocale()));
        return $this->redirectToRoute('app_festival_detail', ['id' => $id]);
    }

    /**
     * pour ajouter un responsable à un festival
     */
    #[Route('/festival/{festId}/addResponsable/{userId}', name: 'app_festival_add_responsable', options: ["expose" => true])]
    public function addResponsabel(FestivalRepository $repository, UtilisateurRepository $userRepo, int $festId, int $userId, UtilisateurUtils $utilisateurUtils, EntityManagerInterface $em, ErrorService $errorService, TranslatorInterface $translator): Response {

        $festival = $repository->find($festId);
        $u = $userRepo->find($userId);

        if (!$festival) {
            return $errorService->MustBeLoggedError();
        }


        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', $translator->trans('user.error.notFound', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $festId]);
        }

        $isBenevole = $utilisateurUtils->isBenevole($u, $festival);
        $isResponsable = $utilisateurUtils->isResponsable($u, $festival);

        if (!$isBenevole) {
            $this->addFlash('error', $translator->trans('user.error.notVolunteer', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $festId]);
        };
        if (!$isResponsable) {
            $festival->addResponsable($u);
            $em->persist($festival);
            $em->flush();
            $this->addFlash('success', $translator->trans('user.success.manager', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $festId]);
        } else {
            $this->addFlash('erreur', '');
            return $errorService->MustBeLoggedError();
        }
    }

    /**
     * pour retirer un responsable à un festival
     */
    #[Route('/festival/{festId}/removeResponsable/{userId}', name: 'app_festival_remove_responsable', options: ["expose" => true])]
    public function removeResponsabel(FestivalRepository $repository, UtilisateurRepository $userRepo, int $festId, int $userId, UtilisateurUtils $utilisateurUtils, EntityManagerInterface $em, ErrorService $errorService, TranslatorInterface $translator): Response {

        $festival = $repository->find($festId);
        $u = $userRepo->find($userId);

        if (!$festival) {
            return $errorService->MustBeLoggedError();
        }


        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', $translator->trans('user.error.notFound', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $festId]);
        }

        $isBenevole = $utilisateurUtils->isBenevole($u, $festival);
        $isResponsable = $utilisateurUtils->isResponsable($u, $festival);

        if (!$isBenevole) {
            $this->addFlash('error', $translator->trans('user.error.notVolunteer', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $festId]);
        };
        if ($isResponsable) {
            $festival->removeResponsable($u);
            $em->persist($festival);
            $em->flush();
            $this->addFlash('success', $translator->trans('user.success.notManager', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $festId]);
        } else {
            $this->addFlash('erreur', "");
            return $errorService->MustBeLoggedError();
        }
    }

    /**
     * pour afficher les détails d'un festival
     */
    #[Route('/festival/{id}', name: 'app_festival_detail', options: ["expose" => true])]
    public function show(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils): Response {
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
        };

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

    /**
     * pour les maps
     */
    #[Route('/festival/all/coordinate', name: 'app_all_festival', methods: ['GET'], options: ['expose' => true])]
    public function allCoordinatesFest(FestivalRepository $repository): JsonResponse {
        $festivals = $repository->findAll();
        $tab = [];
        foreach ($festivals as $festival) {
            $tab[] = [
                'id' => $festival->getId(),
                'nom' => $festival->getNom(),
                'latitude' => $festival->getLat(),
                'longitude' => $festival->getLon(),
                'open' => $festival->isOpen()
            ];
        }
        return new JsonResponse($tab, 200);
    }

    #[Route('/festival/all/map', name: 'app_map', options: ['expose' => true], methods: ['GET'])]
    public function allFestMap(FestivalRepository $repository): Response
    {
        return $this->render('festival/map.html.twig');
    }

    /**
     * pour afficher les demandes de bénévolat d'un festival {id}
     */
    #[Route('/festival/{id}/demandes', name: 'app_festival_benevoles')]
    public function showDemandes(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils, TranslatorInterface $translator): Response {
        $festival = $repository->find($id);
        if (!$festival) {
            throw $this->createNotFoundException($translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
        }
        if (!$this->getUser() instanceof Utilisateur) {
            $this->addFlash('error', $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_auth_login');
        }

        $utilisateurUtils->isOrganisateur($this->getUser(), $festival);
        $benevoles = $festival->getBenevoles();
        $responsables = $festival->getResponsables();

        return $this->render('benevoles/benevoles.html.twig', [
            'controller_name' => 'FestivalController',
            'demandes' => $festival->getDemandesBenevole(),
            'idFest' => $id,
            'benevoles' => $benevoles,
            'responsables' => $responsables,
        ]);
    }

    /**
     * pour afficher le planning d'un festival {id}
     */
    #[Route('/festival/{id}/planning', name: 'app_festival_planning')]
    public function planning(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils, TranslatorInterface $translator): Response {
        $festival = $repository->find($id);
        if (!$festival) {
            throw $this->createNotFoundException($translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
        }

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_auth_login');
        }

        if (!($utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival) || $utilisateurUtils->isBenevole($u, $festival))) {
            $this->addFlash('error', $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        }

        return $this->render('festival/planning.html.twig', [
            'controller_name' => 'FestivalController',
            'festival' => $festival,
            'isOrgaOrResp' => $utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival),
            'userId' => $u->getId(),
        ]);
    }

    /**
     * pour afficher les postes d'un festival {id}
     */
    #[Route('/festival/{id}/postes', name: 'app_festival_postes')]
    public function postes(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils, TranslatorInterface $translator): Response {
        $festival = $repository->find($id);
        if (!$festival) {
            throw $this->createNotFoundException($translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
        }

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_auth_login');
        }

        if (!($utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival) || $utilisateurUtils->isBenevole($u, $festival))) {
            $this->addFlash('error', $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        }

        return $this->render('festival/postes.html.twig', [
            'controller_name' => 'FestivalController',
            'festival' => $festival,
            'isOrgaOrResp' => $utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival),
            'isFestivalOpen' => $festival->isOpen(),
            'watchingOtherUserPreferences' => false,
        ]);
    }

    /**
     * pour accepter une demande de bénévolat d'un user dans un festival
     */
    #[Route('/festival/{id}/demandes/accept/{idUser}', name: 'app_festival_accept_demande')]
    public function acceptDemandeBenevolat(int $id, int $idUser, FestivalRepository $repo, EntityManagerInterface $em, HistoriquePostulationRepository $historiquePostulationRepository, TranslatorInterface $translator): Response {

        $festival = $repo->find($id);
        $demande = $festival->getDemandesBenevole()->findFirst(function (int $_, Utilisateur $u) use ($idUser) {
            return $u->getId() == $idUser;
        });

        if (!$demande) {
            $this->addFlash('error', $translator->trans('demande.error.notFound', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $id]);
        }

        $festival->addBenevole($demande);
        $historiquePostulationRepository->findOneBy(['utilisateur' => $idUser, 'festival' => $id])->setStatut(1);
        $festival->removeDemandesBenevole($demande);

        $em->persist($festival);
        $em->flush();

        $this->addFlash('success', $translator->trans('demande.success.demande', [], 'msgflash', $translator->getLocale()));
        return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $id]);
    }

    #[Route('/festival/{id}/poste', name: 'app_festival_create_poste', methods: ['POST'], options: ["expose" => true])]
    public function createPoste(#[MapEntity] Festival $festival, Request $request, EntityManagerInterface $em, UtilisateurUtils $utilisateurUtils, TranslatorInterface $translator): JsonResponse {
        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale()));
            return new JsonResponse(['error' => $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale())], 403);
        }

        if (!($utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival))) {
            $this->addFlash('error', $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale()));
            return new JsonResponse(['error' => $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale())], 403);
        }
        if ($festival->isOpen()) {
            $this->addFlash('error', $translator->trans('festival.error.alreadyOpen', [], 'msgflash', $translator->getLocale()));
            return new JsonResponse(['error' => $translator->trans('festival.error.alreadyOpen', [], 'msgflash', $translator->getLocale())], 403);
        }

        $poste = new Poste();
        $poste->setFestival($festival);
        $poste->setNom($request->toArray()['nom']);
        $poste->setCouleur($request->toArray()['couleur']);
        $poste->setDescription('');

        $em->persist($poste);
        $em->flush();

        $festival->setValid(0);
        $em->persist($festival);
        $em->flush();

        return new JsonResponse([
            'success' => $translator->trans('poste.success.created', [], 'msgflash', $translator->getLocale()),
            'id' => $poste->getId(),
        ], 200);
    }

    #[Route('/festival/{id}/poste/all', name: 'app_festival_all_poste', methods: ['GET'], options: ["expose" => true])]
    public function allPoste(#[MapEntity] Festival $festival, TranslatorInterface $translator, UtilisateurUtils $utilisateurUtils): JsonResponse {
        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale())], 403);
        }

        $isBenevole = $utilisateurUtils->isBenevole($u, $festival);

        if (!($utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival)) && !$isBenevole) {
            return new JsonResponse(['error' => $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale())], 403);
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
    public function editPoste(FestivalRepository $repository, Request $request, EntityManagerInterface $em, UtilisateurUtils $utilisateurUtils, int $id, int $idPoste, PosteRepository $poste, TranslatorInterface $translator): JsonResponse {

        $f = $repository->find($id);
        $p = $poste->find($idPoste);

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale())], 403);
        }

        $isBenevole = $utilisateurUtils->isBenevole($u, $f);

        if (!($utilisateurUtils->isOrganisateur($u, $f) || $utilisateurUtils->isResponsable($u, $f)) && !$isBenevole) {
            return new JsonResponse(['error' => $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale())], 403);
        }

        if (!$f) {
            return new JsonResponse(['error' => $translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale())], 403);
        }

        if (!$p) {
            return new JsonResponse(['error', $translator->trans('poste.error.notFound', [], 'msgflash', $translator->getLocale())], 403);
        }

        if (!$f->isOpen()) {
            return new JsonResponse(['error' => $translator->trans('festival.error.alreadyOpen', [], 'msgflash', $translator->getLocale())], 403);
        }

        $p->setNom($request->toArray()['nom']);
        $p->setCouleur($request->toArray()['couleur']);
        $p->setDescription($request->toArray()['description']);

        $em->persist($p);
        $em->flush();

        return new JsonResponse(['success' => $translator->trans('poste.success.modified', [], 'msgflash', $translator->getLocale())], Response::HTTP_OK);
    }



    #[Route('/festival/{id}/poste/{idPoste}/delete', name: 'app_festival_delete_poste', methods: ['GET', 'DELETE'], options: ["expose" => true])]
    public function deletePoste(EntityManagerInterface $em, int $id, int $idPoste, FestivalRepository $repository, UtilisateurUtils $utilisateurUtils, PosteRepository $poste, TranslatorInterface $translator): Response {
        $f = $repository->find($id);
        $p = $poste->find($idPoste);

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale())], 403);
        }

        $isBenevole = $utilisateurUtils->isBenevole($u, $f);

        if (!($utilisateurUtils->isOrganisateur($u, $f) || $utilisateurUtils->isResponsable($u, $f)) && !$isBenevole) {
            return new JsonResponse(['error' => $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale())], 403);
        }

        if (!$f) {
            return new JsonResponse(['error' => $translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale())], 403);
        }

        if (!$p) {
            return new JsonResponse(['error', $translator->trans('poste.error.notFound', [], 'msgflash', $translator->getLocale())], 403);
        }
        if (!$f->isOpen()) {
            return new JsonResponse(['error' => $translator->trans('festival.error.alreadyOpen', [], 'msgflash', $translator->getLocale())], 403);
        }

        $em->remove($p);
        $em->flush();

        return new JsonResponse(['success' => $translator->trans('user.success.deleted', [], 'msgflash', $translator->getLocale())], Response::HTTP_OK);
    }

    #[Route('/tache/{id}/affect', name: 'app_benevole_save', methods: ['POST'], options: ["expose" => true])]
    public function affectBenevoles(int $id, Request $request, EntityManagerInterface $em, UtilisateurUtils $utilisateurUtils, TacheRepository $tacheRepository, UtilisateurRepository $ur, TranslatorInterface $translator): Response {
        $tache = $tacheRepository->find($id);
        $festival = $tache->getPoste()->getFestival();

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale())], 403);
        }

        if (!($utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival))) {
            return new JsonResponse(['error' => $translator->trans('user.error.perissionDenied', [], 'msgflash', $translator->getLocale())], 403);
        }

        if (!$festival) {
            return new JsonResponse(['error' => $translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale())], 403);
        }

        if (!$tache) {
            return new JsonResponse(['error', $translator->trans('tache.error.notFound', [], 'msgflash', $translator->getLocale())], 403);
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

        return new JsonResponse(['success' => $translator->trans('user.success.affect', [], 'msgflash', $translator->getLocale())], Response::HTTP_OK);
    }

    #[Route('/festival/{id}/benevole/all', name: 'app_festival_all_benevole',  methods: ['GET'], options: ['expose' => true])]
    public function allBenevoles(PosteUtilisateurPreferencesRepository $prefRepo, #[MapEntity] Festival $festival, UtilisateurUtils $utilisateurUtils, TranslatorInterface $translator): JsonResponse {

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale())], 403);
        }

        if (!($utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival) || $utilisateurUtils->isBenevole($u, $festival))) {
            return new JsonResponse(['error' => $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale())], 403);
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
    public function Benevolespref(#[MapEntity] Tache $tache, UtilisateurUtils $utilisateurUtils, TranslatorInterface $translator): JsonResponse {
        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale())], 403);
        }

        if (!($utilisateurUtils->isOrganisateur($u, $tache->getPoste()->getFestival()) || $utilisateurUtils->isResponsable($u, $tache->getPoste()->getFestival()))) {
            return new JsonResponse(['error' => $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale())], 403);
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
    public function addDebutFinDay(#[MapEntity] Festival $f, Request $request, EntityManagerInterface $em, UtilisateurUtils $utilisateurUtils, TranslatorInterface $translator): Response {
        if ($f == null) {
            return new JsonResponse(['error' => $translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale())], Response::HTTP_NOT_FOUND);
        }
        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale())], Response::HTTP_FORBIDDEN);
        }
        if (!($utilisateurUtils->isOrganisateur($u, $f) || $utilisateurUtils->isResponsable($u, $f))) {
            return new JsonResponse(['error' => $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale())], Response::HTTP_FORBIDDEN);
        }
        $body = json_decode($request->getContent(), true);
        try {
            $dateDebut = new DateTime($body['debut']);
            $dateFin = new DateTime($body['fin']);

            if ($dateDebut > $dateFin) {
                return new JsonResponse(['error' => $translator->trans('date.notValid', [], 'msgflash', $translator->getLocale())], Response::HTTP_BAD_REQUEST);
            } else if ($dateDebut->format('Y-m-d') < $f->getDateDebut()->format('Y-m-d') || $dateDebut->format('Y-m-d') > $f->getDateFin()->format('Y-m-d')) {
                return new JsonResponse(['error' => $translator->trans('date.begin', [], 'msgflash', $translator->getLocale())], Response::HTTP_BAD_REQUEST);
            } else if ($dateFin->format('Y-m-d') < $f->getDateDebut()->format('Y-m-d') || $dateFin->format('Y-m-d') > $f->getDateFin()->format('Y-m-d')) {
                return new JsonResponse(['error' =>$translator->trans('date.end', [], 'msgflash', $translator->getLocale())], Response::HTTP_BAD_REQUEST);
            }

            $c = new Creneaux();
            $c->setDateDebut($dateDebut);
            $c->setDateFin($dateFin);
            $f->addPlagesHoraire($c);

            $em->persist($c);
            $em->flush();
        } catch (\Throwable $th) {
            if ($th instanceof \ErrorException) {
                return new JsonResponse(['error' => $translator->trans('data.notValid', [], 'msgflash', $translator->getLocale())], Response::HTTP_BAD_REQUEST);
            }
            throw $th;
        }

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    #[Route('/festival/{id}/DebutFinDay', name: 'app_festival_get_DebutFinDay', methods: ['GET'], options: ["expose" => true])]
    public function getPlagesHoraires(#[MapEntity] Festival $f, TranslatorInterface $translator): JsonResponse {
        if ($f == null) {
            return new JsonResponse(['error' => $translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale())], Response::HTTP_NOT_FOUND);
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
    public function addTache(#[MapEntity] Festival $f, Request $request, PosteRepository $posteRepository, EntityManagerInterface $em, int $id, UtilisateurUtils $utilisateurUtils, TranslatorInterface $translator): Response {


        if ($f == null) {
            return new JsonResponse(['error' => $translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale())], Response::HTTP_NOT_FOUND);
        }

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale())], Response::HTTP_FORBIDDEN);
        }

        if (!($utilisateurUtils->isOrganisateur($u, $f) || $utilisateurUtils->isResponsable($u, $f))) {
            return new JsonResponse(['error' => $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale())], Response::HTTP_FORBIDDEN);
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
                return new JsonResponse(['error' => $translator->trans('data.notValid', [], 'msgflash', $translator->getLocale())], Response::HTTP_BAD_REQUEST);
            }
            throw $th;
        }

        $p = $posteRepository->find($poste_id);


        if (!$p || $p->getFestival()->getId() != $id) {
            return new JsonResponse(['error' => $translator->trans('poste.error.notFound', [], 'msgflash', $translator->getLocale())], Response::HTTP_NOT_FOUND);
        }

        $t = new Tache();
        $t->setRemarque($description);
        $t->setNombreBenevole($nombreBenevole);

        if ($dateDebut > $dateFin) {
            return new JsonResponse(['error' => $translator->trans('date.notValid', [], 'msgflash', $translator->getLocale())], Response::HTTP_BAD_REQUEST);
        } else if ($dateDebut->format('Y-m-d') < $f->getDateDebut()->format('Y-m-d') || $dateDebut->format('Y-m-d') > $f->getDateFin()->format('Y-m-d')) {
            return new JsonResponse(['error' => $translator->trans('date.begin', [], 'msgflash', $translator->getLocale())], Response::HTTP_BAD_REQUEST);
        } else if ($dateFin->format('Y-m-d') < $f->getDateDebut()->format('Y-m-d') || $dateFin->format('Y-m-d') > $f->getDateFin()->format('Y-m-d')) {
            return new JsonResponse(['error' => $translator->trans('date.end', [], 'msgflash', $translator->getLocale())], Response::HTTP_BAD_REQUEST);
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
    public function getTaches(#[MapEntity] Festival $f, TranslatorInterface $translator): JsonResponse {


        if ($f == null) {
            return new JsonResponse(['error' => $translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale())], Response::HTTP_NOT_FOUND);
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
    public function editTache(Request $request, EntityManagerInterface $em, int $id, int $idTache, FestivalRepository $fRep, TacheRepository $tRep, PosteRepository $pRep, LieuRepository $lRep, TranslatorInterface $translator): Response {

        $f = $fRep->find($id);
        $t = $tRep->find($idTache);

        if (!$f) {
            throw $this->createNotFoundException($translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
        }

        if (!$t) {
            throw $this->createNotFoundException($translator->trans('tache.error.notFound', [], 'msgflash', $translator->getLocale()));
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
                return new JsonResponse(['error' => $translator->trans('data.notValid', [], 'msgflash', $translator->getLocale())], Response::HTTP_BAD_REQUEST);
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

        $this->addFlash('success', $translator->trans('tache.success.modified', [], 'msgflash', $translator->getLocale()));
        return $this->redirectToRoute('app_festival_planning', ['id' => $id]);


        //a modifier
        return $this->render('festival/testmodiftache.html.twig', [
            'controller_name' => 'FestivalController',


        ]);
    }

    #[Route('/festival/{id}/tache/{idTache}/delete', name: 'app_festival_delete_tache', methods: ['DELETE'], options: ["expose" => true])]
    public function deleteTache(EntityManagerInterface $em, int $id, int $idTache, FestivalRepository $fRep, TacheRepository $tRep, TranslatorInterface $translator): Response {

        $f = $fRep->find($id);
        $t = $tRep->find($idTache);

        if (!$f) {
            throw $this->createNotFoundException($translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
        }

        if (!$t) {
            throw $this->createNotFoundException($translator->trans('tache.error.notFound', [], 'msgflash', $translator->getLocale()));
        }

        $em->remove($t);
        $em->flush();

        return new JsonResponse(['success' => $translator->trans('tache.success.deleted', [], 'msgflash', $translator->getLocale())], Response::HTTP_OK);
    }


    #[Route('/festival/{id}/modifier', name: 'app_festival_modifier')]
    public function edit(#[MapEntity] Festival $festival, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, TranslatorInterface $translator): Response {

        if (!$festival) {
            throw $this->createNotFoundException($translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
        }
        if ($festival->isOpen()) {
            $this->addFlash('error', $translator->trans('festival.error.alreadyOpen', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
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
            $festival->setValid(0);
            $em->flush();
            $this->addFlash('success', $translator->trans('festival.success.modified', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        }

        return $this->render('festival/modifier.html.twig', [
            'controller_name' => 'FestivalController',
            'form' => $form->createView(),
            'festival' => $festival,
        ]);
    }

    #[Route('/festival/{id}/archiver', name: 'app_festival_archiver')]
    public function demandeArchiverFestival(FestivalRepository $repository, int $id, TranslatorInterface $translator): Response {
        $festival = $repository->find($id);

        if (!$festival) {
            $this->addFlash('error', $translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        }

        return $this->render('festival/archive.html.twig', [
            'controller_name' => 'FestivalController',
            'festival' => $festival
        ]);
    }

    #[Route('/festival/{id}/archiver/done', name: 'app_festival_archiver_done')]
    public function archiverFestival(FestivalRepository $repository, int $id, EntityManagerInterface $em, TranslatorInterface $translator): Response {
        $festival = $repository->find($id);

        if (!$festival) {
            $this->addFlash('error', $translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        }

        $festival->setIsArchive();

        $em->persist($festival);
        $em->flush();

        return $this->redirectToRoute('app_user_festivals');
    }


    #[Route('/user/{id}/preferences/{idFest}', name: 'app_festival_preferences')]
    public function displayPostes(int $idFest, int $id, PosteUtilisateurPreferencesRepository $posteUtilisateurPreferencesRepository, FestivalRepository $festivalRepository, PosteRepository $posteRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, UtilisateurUtils $uu): Response {

        $festival = $festivalRepository->find($idFest);
        if (!$festival) {
            throw $this->createNotFoundException('Festival non trouvé.');
        }
        $postes = $posteRepository->findBy(["festival" => $festival]);
        $u = $this->getUser();
        
        $postes = $posteRepository->findBy(["festival" => $festival]);

        $pref = $posteUtilisateurPreferencesRepository->findBy(["UtilisateurId" => $u]);

        $isOrgaOrResp = $uu->isOrganisateur($u, $festival) || $uu->isResponsable($u, $festival);
        $routeUserId = $id;

        $watchingOtherUserPreferences = $routeUserId != $u->getId();

        if ($watchingOtherUserPreferences && !$isOrgaOrResp) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette page');
            return $this->redirectToRoute('home');
        }

        return $this->render('festival/postes.html.twig', [
            'utilisateur' => $u,
            'festival' => $festival,
            'isOrgaOrResp' => $uu->isOrganisateur($u, $festival) || $uu->isResponsable($u, $festival),
            'status' => true,
            'watchingOtherUserPreferences' => $watchingOtherUserPreferences,
            'otherUserId' => $routeUserId,
            'isFestivalOpen' => $festival->isOpen(),
        ]);
    }

    #[Route('/festival/{id}/CreateQuestionnaire', name: 'app_festival_create_questionnaire')]
    public function questionnaire(QuestionBenevoleRepository $repository, #[MapEntity] Festival $festival, Request $request,  EntityManagerInterface $em, TranslatorInterface $translator): Response {
        $user = $this->getUser();
        if (!$user || !$user instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }
        if (!$festival) {
            throw $this->createNotFoundException($translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
        } else if ($festival->getOrganisateur() != $user) {
            $this->addFlash('error', $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        } else if ($festival->isOpen()) {
            $this->addFlash('error', $translator->trans('festival.error.alreadyOpen', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        } else {
            $questionBenevole = new QuestionBenevole();
            $questionBenevole->setFestival($festival);
            $form = $this->createForm(QuestionBenevoleType::class, $questionBenevole);

            $form->handleRequest($request);

            $questions = $repository->findBy(["festival" => $festival]);

            if ($form->isSubmitted() && $form->isValid()) {

                $em->persist($questionBenevole);
                $em->flush();
                $this->addFlash('success', $translator->trans('question.success.created', [], 'msgflash', $translator->getLocale()));
                return $this->redirectToRoute('app_festival_create_questionnaire', ['id' => $festival->getId()]);
            }

            return $this->render('festival/CreateQuestionnaire.html.twig', [
                'questions' => $questions,
                'controller_name' => 'FestivalController',
                'form' => $form->createView(),
                'festival' => $festival,
            ]);
        }
    }

    #[Route('/festival/{id}/DeleteQuestion/{idQuestion}', name: 'app_festival_question_delete')]
    public function deleteQuestion(QuestionBenevoleRepository $repository, #[MapEntity] Festival $festival, EntityManagerInterface $em, int $idQuestion, TranslatorInterface $translator): Response {
        $user = $this->getUser();
        if (!$user || !$user instanceof Utilisateur) {
            $this->addFlash('error', $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_auth_login');
        }
        if (!$festival) {
            throw $this->createNotFoundException($translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
        } else if ($festival->getOrganisateur() != $user) {
            $this->addFlash('error', $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        } else if ($festival->isOpen()) {
            $this->addFlash('error', $translator->trans('festival.error.alreadyOpen', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        } else {
            $question = $repository->find($idQuestion);
            if (!$question) {
                throw $this->createNotFoundException($translator->trans('question.error.notFound', [], 'msgflash', $translator->getLocale()));
            } else {
                $em->remove($question);
                $em->flush();
                $this->addFlash('success', $translator->trans('question.success.deleted', [], 'msgflash', $translator->getLocale()));
                return $this->redirectToRoute('app_festival_create_questionnaire', ['id' => $festival->getId()]);
            }
        }
    }

    #[Route('/festival/{id}/ModifyQuestion/{idQuestion}', name: 'app_festival_question_modify')]
    public function modifyQuestion(QuestionBenevoleRepository $repository, #[MapEntity] Festival $festival, Request $request, EntityManagerInterface $em, int $idQuestion, TranslatorInterface $translator): Response {
        $user = $this->getUser();
        if (!$user || !$user instanceof Utilisateur) {
            $this->addFlash('error', $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_auth_login');
        }
        if (!$festival) {
            throw $this->createNotFoundException($translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
        } else if ($festival->getOrganisateur() != $user) {
            $this->addFlash('error', $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        } else if ($festival->isOpen()) {
            $this->addFlash('error', $translator->trans('festival.error.alreadyOpen', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        } else {
            $question = $repository->find($idQuestion);
            if (!$question) {
                throw $this->createNotFoundException($translator->trans('question.error.notFound', [], 'msgflash', $translator->getLocale()));
            } else {
                $form = $this->createForm(ModifierQuestionBenevoleType::class, $question);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $em->persist($question);
                    $em->flush();
                    $this->addFlash('success', $translator->trans('question.success.modified', [], 'msgflash', $translator->getLocale()));
                    return $this->redirectToRoute('app_festival_create_questionnaire', ['id' => $festival->getId()]);
                }
                return $this->render('festival/ModifyQuestion.html.twig', [
                    'controller_name' => 'FestivalController',
                    'form' => $form->createView(),
                    'festival' => $festival,
                ]);
            }
        }
    }

    #[Route('/festival/{id}/gestion', name: 'app_festival_gestion')]
    public function gestion(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils, ValidationRepository $validationRepository, TranslatorInterface $translator): Response {
        $statut = "";

        $festival = $repository->find($id);
        if (!$festival) {
            throw $this->createNotFoundException($translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
        }

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_auth_login');
        } else if ($festival->getOrganisateur() != $u) {
            $this->addFlash('error', $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        } else if ($festival->getIsArchive()) {
            $this->addFlash('error', $translator->trans('festival.error.archived', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        } else {
            if ($festival->getValid() == 1) {
                $statut = $translator->trans('messages.valid_not_open', [], 'gestionfest', $translator->getLocale());
            } else if ($festival->getValid() == 0) {
                $enAttente = $validationRepository->findBy(['festival' => $festival, 'status' => 0]);
                if ($enAttente == null) {
                    $statut = $translator->trans('messages.valid_pending', [], 'gestionfest', $translator->getLocale());
                } else {
                    $statut = $translator->trans('messages.valid_analyse', [], 'gestionfest', $translator->getLocale());
                }
            } else {
                $statut = $translator->trans('messages.valid_rejected', [], 'gestionfest', $translator->getLocale());
            }
        }

        $validations = $validationRepository->findBy(['festival' => $festival]);

        $hasValidations = count($validations) > 0;
        $hasPendingValidation = count(array_filter($validations, function ($v) {
            return $v->getStatus() == 0;
        })) > 0;

        return $this->render('festival/gestionFest.html.twig', [
            'controller_name' => 'FestivalController',
            'festival' => $festival,
            'isOrgaOrResp' => $utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival),
            'userId' => $u->getId(),
            'statut' => $statut,
            'hasPendingValidation' => $hasPendingValidation,
            'hasValidations' => $hasValidations,
        ]);
    }

    /*
     * je laisse cette route au cas ou on veut ajouter le statut d'une demande de festival autre part
     */
    #[Route('/festival/{id}/trackingRequest', name: 'app_festival_trackingRequest')]
    public function trackingRequest(ValidationRepository $validationRepository, #[MapEntity] Festival $festival): JsonResponse {
        $user = $this->getUser();

        if (!$user || !$user instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], 403);
        }

        if ($festival == null) {
            $this->addFlash('error', 'Le festival n\'existe pas');
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], 403);
        } else if ($festival->getOrganisateur() != $user) {
            $this->addFlash('error', 'Vous n\'êtes pas l\'organisateur de ce festival');
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], 403);
        } else if ($festival->getIsArchive()) {
            $this->addFlash('error', 'Le festival est archivé');
            return new JsonResponse(['error' => 'Le festival est archivé'], 403);
        } else {
            $statut = "";
            if ($festival->getValid() == 1) {
                $statut = "Validé";
            } else if ($festival->getValid() == 0) {

                $enAttente = $validationRepository->findBy(['festival' => $festival, 'status' => 0]);
                if ($enAttente == null) {
                    $statut = "en attente de votre demande de validation";
                } else {
                    $statut = "en cours de traitement de validation";
                }
            } else {
                $statut = "Rejeté";
            }

            return new JsonResponse(['statut' => $statut], 200);
        }
    }

    #[Route('/festival/{id}/user/{idUser}/rejectAndSendMotif', name: 'app_festival_rejectAndSendMotif', options: ["expose" => true], methods: ['POST'])]
    public function rejectAndSendMotif(#[MapEntity(id: 'id')] Festival $festival, #[MapEntity(id: 'idUser')] Utilisateur $utilisateur, EntityManagerInterface $em, HistoriquePostulationRepository $historiquePostulationRepository, Request $request, TranslatorInterface $translator): Response {

        if ($festival == null) {
            $this->addFlash('error', $translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_demandesBenevolat');
        } else if ($utilisateur == null) {
            $this->addFlash('error', $translator->trans('user.error.notFound', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_demandesBenevolat');
        } else if ($request->request->get('motif') == null) {
            $this->addFlash('error', $translator->trans('motif.error.void', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_demandesBenevolat');
        } else {
            $demande = $festival->getDemandesBenevole()->findFirst(function (int $_, Utilisateur $u) use ($utilisateur) {
                return $u->getId() == $utilisateur->getId();
            });

            if (!$demande) {
                $this->addFlash('error', $translator->trans('demande.error.notFound', [], 'msgflash', $translator->getLocale()));
                return $this->redirectToRoute('app_festival_demandesBenevolat');
            }
            $historiquePostulationRepository->findOneBy(['utilisateur' => $utilisateur, 'festival' => $festival])->setStatut(-1);

            $festival->removeDemandesBenevole($demande);
            $em->persist($festival);
            $em->flush();


            $this->addFlash('success', $translator->trans('demande.error.rejected', [], 'msgflash', $translator->getLocale()));
            $historiquePostulation = $historiquePostulationRepository->findOneBy(['utilisateur' => $utilisateur, 'festival' => $festival]);
            if ($historiquePostulation == null) {
                $this->addFlash('error', $translator->trans('user.error.noRequest', [], 'msgflash', $translator->getLocale()));
                return $this->redirectToRoute('app_festival_demandesBenevolat');
            } else {
                $motif = $request->request->get('motif');
                $historiquePostulation->setMotif($motif);
                $em->persist($historiquePostulation);
                $em->flush();
                $this->addFlash('success', $translator->trans('motif.success.sent', [], 'msgflash', $translator->getLocale()));
                return $this->redirectToRoute('app_festival_demandesBenevolat');
            }
        }
    }

    #[Route('/festival/{id}/open/page', name: 'app_festival_open_page')]
    public function openFestival(#[MapEntity] Festival $festival, TranslatorInterface $translator): Response {

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', $translator->trans('user.error.notFound', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_auth_login');
        } else if ($festival->getOrganisateur() != $u) {
            $this->addFlash('error', $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        }

        if ($festival == null || $festival->getIsArchive()) {
            $this->addFlash('error', $translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        } else if ($festival->getValid() != 1) {
            $this->addFlash('error', $translator->trans('festival.error.notValid', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_gestion', ['id' => $festival->getId()]);
        } else {
            if ($festival->isOpen()) {
                $this->addFlash('error', $translator->trans('festival.error.alreadyOpen', [], 'msgflash', $translator->getLocale()));
                return $this->redirectToRoute('app_festival_gestion', ['id' => $festival->getId()]);
            }

            return $this->render('postulations/openPostulation.html.twig', [
                'controller_name' => 'FestivalController',
                'festival' => $festival,
            ]);
        }

    }



    #[Route('/festival/{id}/open', name: 'app_festival_open')]
    public function openFest(#[MapEntity] Festival $festival, EntityManagerInterface $em, TranslatorInterface $translator): Response {
        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', $translator->trans('user.error.notFound', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_auth_login');
        } else if ($festival->getOrganisateur() != $u) {
            $this->addFlash('error', $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        }

        if ($festival == null || $festival->getIsArchive()) {
            $this->addFlash('error', $translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        } else if ($festival->getValid() != 1) {
            $this->addFlash('error', $translator->trans('festival.error.notValid', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_gestion', ['id' => $festival->getId()]);
        } else {
            if ($festival->isOpen()) {
                $this->addFlash('error', $translator->trans('festival.error.alreadyOpen', [], 'msgflash', $translator->getLocale()));
                return $this->redirectToRoute('app_festival_gestion', ['id' => $festival->getId()]);
            } else {
                $festival->setOpen(true);
                $em->persist($festival);
                $em->flush();
                $this->addFlash('success', $translator->trans('user.success.open', [], 'msgflash', $translator->getLocale()));
                return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
            }
        }
    }
}
