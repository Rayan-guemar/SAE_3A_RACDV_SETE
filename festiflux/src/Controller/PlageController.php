<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Creneaux;
use App\Entity\Festival;
use App\Entity\Indisponibilite;
use App\Entity\Plage;
use App\Entity\Utilisateur;
use App\Repository\ContactRepository;
use App\Repository\IndisponibiliteRepository;
use App\Repository\PlageRepository;
use App\Repository\TypeContactRepository;
use App\Service\FlashMessageService;
use App\Service\FlashMessageType;
use App\Service\UtilisateurUtils;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlageController extends AbstractController
{
    public function __construct(
        public FlashMessageService $flashMessageService, 
        public PlageRepository $plageRepository, 
        public EntityManagerInterface $em,
        public UtilisateurUtils $utilisateurUtils,
    ) {}
        
    
    #[Route('/festival/{id}/plages', name: 'app_festival_plages')]
    public function plagesHoraire(#[MapEntity]Festival $festival): Response {
        
        if (!$festival) {
            throw $this->createNotFoundException("Le festival n'existe pas");
        }

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        if (!($this->utilisateurUtils->isOrganisateur($u, $festival) || $this->utilisateurUtils->isResponsable($u, $festival) || $this->utilisateurUtils->isBenevole($u, $festival))) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette page');
            return $this->redirectToRoute('home');
        }

        return $this->render('festival/plages.html.twig', [
            'controller_name' => 'FestivalController',
            'festival' => $festival,
            'isOrgaOrResp' => $this->utilisateurUtils->isOrganisateur($u, $festival) || $this->utilisateurUtils->isResponsable($u, $festival),
            'userId' => $u->getId(),
        ]);
    }

    #[Route('/festival/{id}/plages/all', name: 'app_festival_plages_all', methods: ['GET'], options: ["expose" => true])]
    public function getPlagesHoraires(#[MapEntity] Festival $f): JsonResponse {
        if ($f == null) {
            return new JsonResponse(['error' => 'Le festival n\'existe pas'], Response::HTTP_NOT_FOUND);
        }
        $plages = $f->getPlages();
        $tab = [];
        foreach ($plages as $plage) {
            $c = $plage->getCreneau();
            $tab[] = [
                'id' => $plage->getId(),
                'debut' => $c->getDateDebut(),
                'fin' => $c->getDateFin(),
            ];
        }
        return new JsonResponse($tab, 200);
    }

    #[Route('/festival/{id}/plages/add', name: 'app_festival_plages_add', methods: ['POST'], options: ["expose" => true])]
    public function addPlage(#[MapEntity] Festival $f, Request $request): Response {
        if ($f == null) {
            return new JsonResponse(['error' => 'Le festival n\'existe pas'], Response::HTTP_NOT_FOUND);
        }
        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour accéder à cette page'], Response::HTTP_FORBIDDEN);
        }
        if (!($this->utilisateurUtils->isOrganisateur($u, $f) || $this->utilisateurUtils->isResponsable($u, $f))) {
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
            $this->em->persist($c);
            
            $p = new Plage();
            $p->setCreneau($c);
            $p->setFestival($f);

            $this->em->persist($p);
            $this->em->flush();

        } catch (\Throwable $th) {
            if ($th instanceof \ErrorException) {
                return new JsonResponse(['error' => "Les données ne sont pas valides"], Response::HTTP_BAD_REQUEST);
            }
            throw $th;
        }

        return new JsonResponse(status: Response::HTTP_CREATED);
    }


    #[Route('/festival/plages/{id}/delete', name: 'app_festival_plages_delete', methods: ['GET'], options: ["expose" => true])]
    public function plagesHoraireDelete( int $id): Response {
    
      

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            return $this->json(['error' => 'Vous devez être connecté pour accéder à cette page'], 403);
        }

        $plage = $this->plageRepository->findOneBy(['id' => $id]);
        if (!$plage) {
            return $this->json(['error' => 'La plage horaire n\'existe pas'], 404);
        }

        $festival = $plage->getFestival();

        if (!($this->utilisateurUtils->isOrganisateur($u, $festival) || $this->utilisateurUtils->isResponsable($u, $festival) || $this->utilisateurUtils->isBenevole($u, $festival))) {
            return $this->json(['error' => 'Vous n\'avez pas accès à cette action'], 403);
        }

        $this->em->remove($plage);
        $this->em->flush();


        return $this->json(['message' => 'La plage horaire a été supprimée'], 200);
    }

}

