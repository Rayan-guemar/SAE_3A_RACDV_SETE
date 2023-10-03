<?php

namespace App\Controller;

use App\Entity\DemandeFestival;
use App\Entity\Festival;
use App\Repository\DemandeFestivalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DemandeFestivalController extends AbstractController {
    #[Route('/demandefestival', name: 'app_demandefesitval_all')]
    public function all(DemandeFestivalRepository $demandeFestivalRepository): Response {


        // TODO : vérifier que l'utilisateur est bien un admin
        $demandesFestivals = $demandeFestivalRepository->findAll();

        return $this->render('demandefestival/index.html.twig', [
            'controller_name' => 'FestivalController',
            'demandes' => $demandesFestivals
        ]);
    }

    #[Route('/demandefestival/add', name: 'app_demandefestival_add')]
    public function add(Request $req, EntityManagerInterface $em): Response {
        $demandeFestival = new DemandeFestival();

        $form = $this->createForm(DemandeFestivalType::class, $demandeFestival);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $demandeFestival->setOrganisateurFestival($this->getUser());
            $em->persist($demandeFestival);
            $em->flush();
            return $this->redirectToRoute('accueil');
        }

        return $this->render('demandefestival/demandeFestival.html.twig', [
            'controller_name' => 'FestivalController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/demandefestival/accept/{id}', name: 'app_demandefestival_accept')]
    public function accept(DemandeFestivalRepository $demandeFestivalRepository, EntityManagerInterface $em, int $id): Response {
        // TODO : vérifier que l'utilisateur est bien un admin
        $demandeFestival = $demandeFestivalRepository->find($id);
        if ($demandeFestival === null) {
            throw $this->createNotFoundException('Demande de festival non trouvée');
        }

        $festivals = new Festival();
        $festivals->setNom($demandeFestival->getNomFestival());
        $festivals->setDateDebut($demandeFestival->getDateDebutFestival());
        $festivals->setDateFin($demandeFestival->getDateFinFestival());
        $festivals->setDescription($demandeFestival->getDescriptionFestival());
        $festivals->setOrganisateur($demandeFestival->getOrganisateurFestival());
        $festivals->setLieu($demandeFestival->getLieuFestival());
        $festivals->setAffiche($demandeFestival->getAfficheFestival());

        $em->persist($festivals);
        $em->remove($demandeFestival);
        $em->flush();


        return $this->redirectToRoute('app_demandefestival_all');
    }
}
