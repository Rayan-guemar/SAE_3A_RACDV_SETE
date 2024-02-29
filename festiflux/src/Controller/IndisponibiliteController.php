<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Creneaux;
use App\Entity\Festival;
use App\Entity\Indisponibilite;
use App\Entity\Utilisateur;
use App\Repository\ContactRepository;
use App\Repository\IndisponibiliteRepository;
use App\Repository\TypeContactRepository;
use App\Service\FlashMessageService;
use App\Service\FlashMessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndisponibiliteController extends AbstractController
{
    public function __construct(
        public FlashMessageService $flashMessageService, 
        public IndisponibiliteRepository $indisponibiliteRepository, 
        public EntityManagerInterface $em
    ) {}
        
    #[Route('/user/{userId}/indisponibilite/{festivalId}/manage', name: 'app_user_indispo_manage', methods: ['GET'])]
    public function manageIndispo(Request $req, #[MapEntity(id: "userId")] Utilisateur $user, #[MapEntity(id: "festivalId")] Festival $festival): Response {
        $u = $this->getUser();
        
        if (!$u instanceof Utilisateur) {
            $this->flashMessageService->add(FlashMessageType::ERROR, "Vous n'êtes pas connecté");
            return $this->redirectToRoute('home');
        }
    
        if ($u->getId() !== $user->getId() && $festival->getOrganisateur() !== $u) {
            $this->flashMessageService->add(FlashMessageType::ERROR, "Vous n'avez pas le droit de voir les indisponibilités de cet utilisateur");
            return $this->redirectToRoute('home');
        }
    
        return $this->render('indisponibilite/index.html.twig', [
            'controller_name' => 'IndisponibiliteController',
            'utilisateur' => $user,
            'festival' => $festival,
            'isOrga' => $festival->getOrganisateur() === $u
        ]);
    }
        
    #[Route('/user/{userId}/indisponibilite/{festivalId}/', name: 'app_user_indispo', methods: ['GET'], options: ['expose' => true])]
    public function getIndispo(Request $req, #[MapEntity(id: "userId")] Utilisateur $user, #[MapEntity(id: "festivalId")] Festival $festival): Response {
        $u = $this->getUser();
        
        if (!$u instanceof Utilisateur) {
            $this->flashMessageService->add(FlashMessageType::ERROR, "Vous n'êtes pas connecté");
            return $this->redirectToRoute('home');
        }

        if ($u->getId() !== $user->getId() && $festival->getOrganisateur() !== $u) {
            $this->flashMessageService->add(FlashMessageType::ERROR, "Vous n'avez pas le droit de voir les indisponibilités de cet utilisateur");
            return $this->redirectToRoute('home');
        }

        $indispo = $this->indisponibiliteRepository->findBy(['utilisateur' => $user, 'festival' => $festival]);
        $creneaux = [];
        foreach ($indispo as $i) {
            $c = $i->getCreneau();
            $creneaux[] = [
                'id' => $c->getId(),
                'debut' => $c->getDateDebut()->format('Y-m-d H:i:s'),
                'fin' => $c->getDateFin()->format('Y-m-d H:i:s'),
            ];
        }
        

        return new JsonResponse($creneaux, 200);
    }

    #[Route('/user/{userId}/indisponibilite/{festivalId}/add', name: 'app_user_indispo_add', methods: ['POST'], options: ['expose' => true])]
    public function addIndispo(Request $req, #[MapEntity(id: "userId")] Utilisateur $user, #[MapEntity(id: "festivalId")] Festival $festival): Response {
        $u = $this->getUser();
        
        if (!$u instanceof Utilisateur) {
            $this->flashMessageService->add(FlashMessageType::ERROR, "Vous n'êtes pas connecté");
            return new JsonResponse("Vous n'êtes pas connecté", 403, [], true);
        }

        if ($u->getId() !== $user->getId() && $festival->getOrganisateur() !== $u) {
            $this->flashMessageService->add(FlashMessageType::ERROR, "Vous n'avez pas le droit de voir les indisponibilités de cet utilisateur");
            return new JsonResponse("Vous n'avez pas le droit de voir les indisponibilités de cet utilisateur", 403, [], true);
        }

        $data = json_decode($req->getContent(), true);

        $date_debut = new \DateTime($data['debut']);
        $date_fin = new \DateTime($data['fin']);

        $c = new Creneaux();

        $c->setDateDebut($date_debut);
        $c->setDateFin($date_fin);

        
        $this->em->persist($c);
        
        $indispo = new Indisponibilite();
        $indispo->setCreneau($c);
        $indispo->setUtilisateur($user);
        $indispo->setFestival($festival);

        $this->em->persist($indispo);

        $this->em->flush();

        return new JsonResponse(" Indisponibilté à bien été ajouté" , 200);
    }

}
