<?php

namespace App\Controller;

use App\Entity\DemandeFestival;
use App\Entity\Festival;
use App\Form\DemandeFestivalType;
use App\Repository\DemandeFestivalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class DemandeFestivalController extends AbstractController {
    #[Route('/demandefestival', name: 'app_demandefestival_all')]
    public function all(DemandeFestivalRepository $demandeFestivalRepository): Response {


        // TODO : vérifier que l'utilisateur est bien un admin
        $demandesFestivals = $demandeFestivalRepository->findAll();

        return $this->render('demande_festival/index.html.twig', [
            'controller_name' => 'FestivalController',
            'demandes' => $demandesFestivals
        ]);
    }

    #[Route('/demandefestival/add', name: 'app_demandefestival_add', methods: ['GET', 'POST'])]
    public function add(Request $req, EntityManagerInterface $em, SluggerInterface $slugger) {
        $demandeFestival = new DemandeFestival();

        $form = $this->createForm(DemandeFestivalType::class, $demandeFestival,
                                        [
                                        'method'=> "POST",
                                        'action'=>$this->generateUrl('home')
                                    ]);

        $form->handleRequest($req);
        if($req->isMethod('POST')) {
            $this->denyAccessUnlessGranted('ROLE_USER');
            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->get('dateFinFestival')->getData() > $form->get('dateDebutFestival')->getData()) {

                    $affiche = $form->get('afficheFestival')->getData();

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
                            throw new \Exception('Erreur lors de l\'upload de l\'affiche');
                        }

                        $demandeFestival->setAfficheFestival($newFilename);
                    }


                    $demandeFestival->setOrganisateurFestival($this->getUser());
                    $demandeFestival->setLat($form->get('lat')->getData());
                    $demandeFestival->setLon($form->get('lon')->getData());
                    $em->persist($demandeFestival);
                    $em->flush();
                    $this->addFlash('success', 'Demande de festival envoyée');
                    return $this->redirectToRoute('demandefestival');
                }
                else {
                $this->addFlash('error', 'Les dates de votre festival ne sont pas conforme !');
                return $this->redirectToRoute('app_demandefestival_add');
                }

            }else{
                $this->addFlash('error', 'une erreur est survenue lors de la soumission du formulaire !');
                return $this->redirectToRoute('app_demandefestival_add');
            }
        }
        return $this->render('demande_festival/add.html.twig', [
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
        $festivals->setLat($demandeFestival->getLat());
        $festivals->setLon($demandeFestival->getLon());
        $festivals->setAffiche($demandeFestival->getAfficheFestival());

        $em->persist($festivals);
        $em->remove($demandeFestival);
        $em->flush();

        $this->addFlash('success', 'Demande de festival acceptée');
        return $this->redirectToRoute('app_demandefestival_all');
    }
}
