<?php

namespace App\Controller;

use App\Entity\DemandeFestival;
use App\Entity\Festival;
use App\Entity\Tag;
use App\Form\DemandeFestivalType;
use App\Repository\DemandeFestivalRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DemandeFestivalController extends AbstractController {

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/demandefestival', name: 'app_demandefestival_all', options: ["expose" => true], methods: ['GET'])]
    public function all(DemandeFestivalRepository $demandeFestivalRepository): Response {

        $demandesFestivals = $demandeFestivalRepository->findAll();

        return $this->render('demande_festival/index.html.twig', [
            'controller_name' => 'FestivalController',
            'demandes' => $demandesFestivals
        ]);
    }

    #[Route('/demandefestival/add', name: 'app_demandefestival_add', methods: ['GET', 'POST'])]
    public function add(Request $req, EntityManagerInterface $em, SluggerInterface $slugger) {
        $demandeFestival = new DemandeFestival();



        $form = $this->createForm(DemandeFestivalType::class, $demandeFestival);


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
                    return $this->redirectToRoute('home');
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
    public function accept(TagRepository $tagRepository,DemandeFestivalRepository $demandeFestivalRepository, EntityManagerInterface $em, int $id): Response {
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

        $tag_arr = explode (",", $demandeFestival->getTags());
        foreach ($tag_arr as $tagName){
            $verif = ($tagRepository)->findBy(["nom"=>$tagName]);
            ($verif!= null)? $tag = $verif[0] : $tag = new Tag($tagName); $em->persist($tag);
            $festivals->addTag($tag);
        }


        $em->persist($festivals);
        $em->remove($demandeFestival);
        $em->flush();

        $this->addFlash('success', 'Demande de festival acceptée');
        return $this->redirectToRoute('app_demandefestival_all');
    }

    #[Route('/demandefestival/reject/{id}', name: 'app_demandefestival_reject')]
    public function reject(DemandeFestivalRepository $demandeFestivalRepository, EntityManagerInterface $em, int $id ): Response {

        $demandeFestival = $demandeFestivalRepository->find($id);


        if ($demandeFestival === null) {
            throw $this->createNotFoundException('Demande de festival non trouvée');
        }

        $em->remove($demandeFestival);
        $em->flush();


        $this->addFlash('success', 'La demande a bien été rejetée');
        return $this->redirectToRoute('app_demandefestival_all');
    }

}
