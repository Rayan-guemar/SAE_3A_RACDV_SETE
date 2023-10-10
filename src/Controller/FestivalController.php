<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\FestivalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tache;
use App\Form\TacheType;
use App\Entity\Utilisateur;
use App\Service\UtilisateurUtils;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Creneaux;
use App\Entity\Lieu;

class FestivalController extends AbstractController {
    #[Route('/', name: 'home')]
    public function index(FestivalRepository $repository): Response {
        return $this->redirectToRoute('app_festival_all');
    }


    #[Route('/festival/all', name: 'app_festival_all')]
    public function all(FestivalRepository $repository, Request $request): Response {
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
    public function apply(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils, EntityManagerInterface $em): Response {

        $festival = $repository->find($id);
        if (!$festival) {
            throw $this->createNotFoundException("Le festival n'existe pas");
        }

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        $isBenevole = $utilisateurUtils->isBenevole($u, $festival);
        $isResponsable = $utilisateurUtils->isResponsable($u, $festival);
        $isOrganisateur = $utilisateurUtils->isOrganisateur($u, $festival);

        if ($isBenevole || $isResponsable || $isOrganisateur) {
            $this->addFlash('error', 'Vous êtes déjà inscrit à ce festival');
            return $this->redirectToRoute('app_festival_detail', ['id' => $id]);
        };

        $festival->addBenevole($u);
        $em->persist($festival);
        $em->flush();

        return $this->redirectToRoute('app_festival_detail', ['id' => $id]);
    }

    #[Route('/festival/{id}', name: 'app_festival_detail')]
    public function show(FestivalRepository $repository, int $id, UtilisateurUtils $utilisateurUtils): Response {
        $festival = $repository->find($id);

        if (!$festival) {
            throw $this->createNotFoundException("Le festival n'existe pas");
        }

        $isBenevole = false;
        $isResponsable = false;
        $isOrganisateur = false;
        $u = $this->getUser();
        if ($u && $u instanceof Utilisateur) {
            $isBenevole = $utilisateurUtils->isBenevole($u, $festival);
            $isResponsable = $utilisateurUtils->isResponsable($u, $festival);
            $isOrganisateur = $utilisateurUtils->isOrganisateur($u, $festival);
        };


        return $this->render('festival/detailfest.html.twig', [
            'controller_name' => 'FestivalController',
            'festival' => $festival,
            'isBenevole' => $isBenevole,
            'isResponsable' => $isResponsable,
            'isOrganisateur' => $isOrganisateur
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



        return $this->render('demandes_benevolat/demandesBenevole.html.twig', [
            'controller_name' => 'FestivalController',
            'demandes' => $festival->getDemandesBenevole(),
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
