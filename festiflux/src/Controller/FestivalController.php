<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\FestivalRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tache;
use App\Form\TacheType;
use App\Entity\Utilisateur;
use App\Form\DemandeFestivalType;
use App\Repository\DemandeFestivalRepository;
use App\Service\ErrorService;
use App\Service\FlashMessageService;
use App\Service\FlashMessageType;
use App\Service\UtilisateurUtils;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Creneaux;
use App\Entity\Festival;
use App\Entity\Lieu;
use App\Repository\DemandeBenevoleRepository;

class FestivalController extends AbstractController {
    #[Route('/', name: 'home')]
    public function index(FestivalRepository $repository): Response {
        return $this->redirectToRoute('app_festival_all');
    }


    #[Route('/festival/all', name: 'app_festival_all')]
    public function all(FestivalRepository $repository, Request $request, FlashMessageService $flashMessageService): Response {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $festivals = $repository->findBySearch($searchData);

            return $this->render('festival/index.html.twig', [
                'form' => $form->createView(),
                'festivals' => $festivals
            ]);
        }

        $festivals = $repository->findAll();
        return $this->render('festival/index.html.twig', [
            'form' => $form->createView(),
            'festivals' => $festivals
        ]);
    }

    #[Route('/festival/{id}/apply', name: 'app_festival_apply_volunteer')]
    public function apply(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils, EntityManagerInterface $em, FlashMessageService $flashMessageService, ErrorService $errorService): Response {

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
        };

        $festival->addDemandesBenevole($u); 
        $em->persist($festival);
        $em->flush();

        $this->addFlash('success', 'Demande de bénévolat envoyée');
        return $this->redirectToRoute('app_festival_detail', ['id' => $id]);
    }
//    #[Route('/festival/{festId}/verifResponsable/{userId}', name: 'app_festival_verif_responsable', options: ["expose" => true] )]
//    public function verifResponsabel(FestivalRepository $repository, UtilisateurRepository $userRepo, int $festId, int $userId, UtilisateurUtils $utilisateurUtils, EntityManagerInterface $em,  ErrorService $errorService) {
//
//        $festival = $repository->find($festId);
//        $u = $userRepo->find($userId);
//
//        if (!$festival) {
//            return $errorService->MustBeLoggedError();
//        }
//
//
//        if (!$u || !$u instanceof Utilisateur) {
//            return $errorService->MustBeLoggedError();
//        }
//
//        $isBenevole = $utilisateurUtils->isBenevole($u, $festival);
//        $isResponsable = $utilisateurUtils->isResponsable($u, $festival);
//
//        if (!$isBenevole) {
//            $this->addFlash('error', "cet utilisateur n'êtes pas bénévole pour ce festival");
//            return $errorService->MustBeLoggedError();
//        };
//        return new JsonResponse($isResponsable);
//    }

    #[Route('/festival/{festId}/addResponsable/{userId}', name: 'app_festival_add_responsable', options: ["expose" => true] )]
    public function addResponsabel(FestivalRepository $repository, UtilisateurRepository $userRepo, int $festId, int $userId, UtilisateurUtils $utilisateurUtils, EntityManagerInterface $em,  ErrorService $errorService) {

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
        };
        if (!$isResponsable) {
            $festival->addResponsable($u);
            $em->persist($festival);
            $em->flush();
            $this->addFlash('success', 'Cet utilisateur est maintenant responsable pour ce festival');
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $festId]);
        }else{
            $this->addFlash('erreur', '');
            return $errorService->MustBeLoggedError();

        }
    }
    #[Route('/festival/{festId}/removeResponsable/{userId}', name: 'app_festival_remove_responsable', options: ["expose" => true] )]
    public function removeResponsabel(FestivalRepository $repository, UtilisateurRepository $userRepo, int $festId, int $userId, UtilisateurUtils $utilisateurUtils, EntityManagerInterface $em, ErrorService $errorService) {

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
        };
        if ($isResponsable) {
            $festival->removeResponsable($u);
            $em->persist($festival);
            $em->flush();
            $this->addFlash('success', "Cet utilisateur n'est maintenant plus responsable pour ce festival");
            return $this->redirectToRoute('app_festival_demandesBenevolat', ['id' => $festId]);
        }else{
            $this->addFlash('erreur', "");
            return $errorService->MustBeLoggedError();
        }

    }
    
    #[Route('/festival/{id}', name: 'app_festival_detail')]
    public function show(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils): Response {
        $festival = $repository->find($id);

        if (!$festival) {
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


        return $this->render('festival/detailfest.html.twig', [
            'controller_name' => 'FestivalController',
            'festival' => $festival,
            'isBenevole' => $isBenevole,
            'isResponsable' => $isResponsable,
            'isOrganisateur' => $isOrganisateur,
            'hasApplied' => $hasApplied,
        ]);
    }

    #[Route('/festival/{id}/demandes', name: 'app_festival_demandesBenevolat')]
    public function showDemandes(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils): Response {
        $festival = $repository->find($id);
        if (!$festival) {
            throw $this->createNotFoundException("Le festival n'existe pas");
        }
        if (!$this->getUser() instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        $utilisateurUtils->isOrganisateur($this->getUser(), $festival);
        $benevoles = $festival->getBenevoles();
        $responsables = $festival->getResponsables();

        return $this->render('demandes_benevolat/demandesBenevole.html.twig', [
            'controller_name' => 'FestivalController',
            'demandes' => $festival->getDemandesBenevole(),
            'idFest' => $id,
            'benevoles'=>$benevoles,
            'responsables'=>$responsables,
        ]);
    }

    #[Route('/festival/{id}/demandes/accept/{idUser}', name: 'app_festival_accept_demande')]
    public function acceptDemandeBenevolat(int $id, int $idUser, FestivalRepository $repo, EntityManagerInterface $em ) {

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
        return $this->render('demandes_benevolat/demandesBenevole.html.twig', [
            'controller_name' => 'FestivalController',
            'demandes' => $festival->getDemandesBenevole(),
            'idFest' => $id,
            'benevoles' => $festival->getBenevoles()
        ]);
    }

    #[Route('/festival/{id}/demandes/reject/{idUser}', name: 'app_festival_reject_demande')]
    public function rejectDemandeBenevolat(int $id, int $idUser, FestivalRepository $repo, EntityManagerInterface $em, DemandeBenevoleRepository $demandeRepo ) {

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
        return $this->render('demandes_benevolat/demandesBenevole.html.twig', [
            'controller_name' => 'FestivalController',
            'demandes' => $festival->getDemandesBenevole(),
            'idFest' => $id,
            'benevoles' => $festival->getBenevoles()
        ]);
    }


    #[Route('/festival/{id}/tache/create', name: 'app_festival_tache_create')]
    public function createTask(FestivalRepository $fr, Request $request, EntityManagerInterface $em, int $id): Response {

        $tache = new Tache();
        $festival = $fr->find($id);

        $form = $this->createForm(TacheType::class, $tache);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tache->setFestival($festival);

            $creneau = new Creneaux();
            $creneau->setDateDebut($form->get('heureDebut')->getData());
            $creneau->setDateFin($form->get('heureFin')->getData());
            $creneau->setFestival($festival);

            $lieu = new Lieu();
            $lieu->setNomLieu($form->get('lieu')->getData());
            $lieu->setFestival($festival);
            $tache->setLieu($lieu);



            $em->persist($creneau);
            $em->persist($lieu);
            $em->persist($tache);

            $em->flush();
            $this->addFlash('success', 'La tâche a bien été créée');
            return $this->redirectToRoute('app_festival_detail', ['id' => $id]);
        }

        return $this->render('festival/createTask.html.twig', [
            'controller_name' => 'FestivalController',
            'form' => $form->createView(),
        ]);
    }
}
