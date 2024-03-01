<?php

namespace App\Controller;

use App\Entity\Festival;
use App\Entity\Preference;
use App\Entity\Postulations;
use App\Entity\Reponse;
use App\Entity\Utilisateur;
use App\Repository\IndisponibiliteRepository;
use App\Repository\PostulationsRepository;
use App\Repository\PreferenceRepository;
use App\Repository\QuestionBenevoleRepository;
use App\Service\FlashMessageService;
use App\Service\FlashMessageType;
use App\Service\UtilisateurUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostulationController extends AbstractController {


    public function __construct(
        private PostulationsRepository $postulationRepository,
        private EntityManagerInterface $em,
        private UtilisateurUtils $utilisateurUtils,
        private FlashMessageService $flashMessageService,
        private IndisponibiliteRepository $indisponibiliteRepository,
        private QuestionBenevoleRepository $questionBenevoleRepository,
        private PreferenceRepository $preferenceRepository,
    ) {}

    #[Route('/festival/{id}/postulations', name: 'app_festival_postulations')]
    public function festivalPostulations(#[MapEntity] Festival $festival, ): Response {
        $postulations = $this->postulationRepository->findBy(['festival' => $festival, 'status' => Postulations::STATUS_PENDING, 'faite' => true]);
        return $this->render('festival/postulations.html.twig', [
            'festival' => $festival,
            'postulations' => $postulations,
        ]);
    }

    #[Route('/postulations', name: 'app_postulations')]
    public function usersPostulations(): Response {
        $user = $this->getUser();
        if (!$user || !$user instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        $postulations = $user->getPostulations();
        return $this->render('postulations/index.html.twig', [
            'postulations' => $postulations,
        ]);
    }

    #[Route('/postulations/{id}', name: 'app_postulations_postulation')]
    public function postulation(#[MapEntity] Postulations $postulation): Response {
        // a mettre un peu partout
        if ($postulation->getStatus() != 0) {
            $this->addFlash('error', 'Cette demande a déjà été traité');
            return $this->redirectToRoute('app_festival_detail', ['id' => $postulation->getFestival()->getId()]);
        }

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        if (!$postulation) {
            $this->addFlash('error', 'Cette demande n\'existe pas');
            return $this->redirectToRoute('home');
        }

        $festival = $postulation->getFestival();

        if ($u != $postulation->getUtilisateur() && !($this->utilisateurUtils->isOrganisateur($u, $festival))) {
            $this->addFlash('error', 'Vous n\'êtes pas organisateur de ce festival');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        };

        if ($postulation->isFaite()) {
            return $this->render('postulations/postulation_faite.html.twig', [
                'postulation' => $postulation,
            ]);
        }

        $isFormDone = $festival->getQuestionBenevoles()->isEmpty() || !$postulation->getReponses()->isEmpty();
        $areIndisposDone = $this->indisponibiliteRepository->areIndisposDoneByUserAndFestival($postulation->getUtilisateur(), $festival);
        $arePreferencesDone = $this->preferenceRepository->arePreferencesDoneByUserAndFestival($u, $festival);

        return $this->render('postulations/postulation.html.twig', [
            'postulation' => $postulation,
            'isFormDone' => $isFormDone,
            'areIndisposDone' => $areIndisposDone,
            'arePreferencesDone' => $arePreferencesDone
        ]);
    }
    #[Route('festival/{id}/postulation/add', name: 'app_postulations_add')]
    public function potulationAdd(#[MapEntity] Festival $festival, Request $req): Response {
        
        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        if ($this->utilisateurUtils->isOrganisateur($u, $festival)) {
            $this->addFlash('error', 'Vous ne pouvez pas postuler à votre propre festival');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        };

        if ($this->utilisateurUtils->isBenevole($u, $festival)) {
            $this->addFlash('error', 'Vous participez déjà à ce festival');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        }

        $postulation =  $this->postulationRepository->findOneBy(['festival' => $festival, 'utilisateur' => $u]);
        $p = null;
        if ($postulation) {

            if ($postulation->isFaite()) {
                $this->addFlash('error', 'Vous avez déjà postulé à ce festival');
                return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
            }else {
                $p = $postulation;
            }
        } else {
            $p = new Postulations();
            $p->setFestival($festival);
            $p->setUtilisateur($u);
            $p->setStatus(0);
    
            $this->em->persist($p);
            $this->em->flush();
            return $this->redirectToRoute('app_postulations_postulation', ['id' => $p->getId()]);

        }


        return $this->redirectToRoute('app_festival_detail', ['id' => $p->getFestival()->getId()]);
    }

    #[Route('/postulations/{id}/send', name: 'app_postulations_send')]
    public function postulationSend(#[MapEntity] Postulations $postulation, Request $req ): Response {

        if ($postulation->getStatus() != 0) {
            $this->addFlash('error', 'Cette demande a déjà été traité');
            return $this->redirectToRoute('app_festival_detail', ['id' => $postulation->getFestival()->getId()]);
        }

        $u = $this->getUser();
        $festival = $postulation->getFestival();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        if ($this->utilisateurUtils->isOrganisateur($u, $festival)) {
            $this->addFlash('error', 'Vous ne pouvez pas postuler à votre propre festival');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        };

        if ($this->utilisateurUtils->isBenevole($u, $festival)) {
            $this->addFlash('error', 'Vous participez déjà à ce festival');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        }

        if (!$postulation) {
            $this->addFlash('error', '');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        }


        $isFormDone = $festival->getQuestionBenevoles()->isEmpty() || !$postulation->getReponses()->isEmpty();

        if (!$isFormDone) {
            $this->addFlash('error', 'Vous devez remplir le formulaire, vos indisponibilités et vos préférences pour postuler');
            return $this->redirectToRoute('app_postulations_add', ['id' => $festival->getId()]);
        }

        $postulation->setFaite(true);
        $this->em->persist($postulation);
        $this->em->flush();

        $this->flashMessageService->add(FlashMessageType::SUCCESS, 'Votre postulation a bien été prise en compte');
        return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
    }



    #[Route('/postulations/{id}/form', name: 'app_postulations_form')]
    public function postulationForm(#[MapEntity] Postulations $postulation, Request $req ): Response {

        if ($postulation->getStatus() != 0) {
            $this->addFlash('error', 'Cette demande a déjà été traité');
            return $this->redirectToRoute('app_festival_detail', ['id' => $postulation->getFestival()->getId()]);
        }

        $u = $this->getUser();
        $festival = $postulation->getFestival();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }
        
        if (!$postulation) {
            $this->addFlash('error', '');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        }

        if ($req->isMethod('POST')) {

            
            $formData = $req->request->all();

            foreach ($formData['responses'] as $questionId => $responseContent) {
                $response = new Reponse();
                $question = $this->questionBenevoleRepository->find($questionId);
                $response
                    ->setQuestion($question)
                    ->setContenue($responseContent);
                $response->setPostulation($postulation);
                $this->em->persist($response);
            }

            $this->em->persist($postulation);
            $this->em->flush();

            $this->flashMessageService->add(FlashMessageType::SUCCESS, 'Votre postulation a bien été prise en compte');
            return $this->redirectToRoute('app_postulations_postulation', ['id' => $postulation->getId()]);
        }

        $questions = $festival->getQuestionBenevoles();

        
        if ($questions->isEmpty()) {
            $this->addFlash('erreur', 'Le formulaire n\'existe pas pour ce festival');
            return $this->redirectToRoute('app_postulations_postulation', ['id' => $postulation->getId()]);
        }
        
        if ($postulation->isFaite()) {
            if (!($u == $postulation->getUtilisateur() || $this->utilisateurUtils->isOrganisateur($u, $festival))) {
                 $this->addFlash('error', 'Vous n\'avez pas accès à cette page');
                 return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
             }
             
             return $this->render('postulations/form_filled.html.twig', [
                 'festival' => $festival,
                 'questions' => $questions,
                 'postulation' => $postulation,
             ]);
        }


        if (!($u == $postulation->getUtilisateur())) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette page');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        }
        


        return $this->render('postulations/form.html.twig', [
            'festival' => $festival,
            'questions' => $questions,
            'postulation' => $postulation,
        ]);
    }

    #[Route('postulations/{id}/accept', name: 'app_postulations_accept', methods: ['POST'])]
    public function postulationAccept(#[MapEntity] Postulations $postulation): Response {

        if ($postulation->getStatus() != 0) {
            $this->addFlash('error', 'Cette demande a déjà été traité');
            return $this->redirectToRoute('app_festival_detail', ['id' => $postulation->getFestival()->getId()]);
        }

        $u = $this->getUser();

        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        $festival = $postulation->getFestival();

        if (!$this->utilisateurUtils->isOrganisateur($u, $festival)) {
            $this->addFlash('error', 'Vous ne pouvez pas effectué cette action');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        };

        if (!$postulation) {
            $this->addFlash('error', 'Cette demande n\'existe pas');
            return $this->redirectToRoute('app_festival_postulations', ['id' => $festival->getId()]);
        }

        if ($postulation->getStatus() !== Postulations::STATUS_PENDING) {
            $this->addFlash('error', 'Cette demande a déjà été traité');
            return $this->redirectToRoute('app_festival_postulations', ['id' => $festival->getId()]);
        };


        $postulation->setStatus(1);
        $festival->addBenevole($u);
        $this->em->persist($festival);

        $this->flashMessageService->add(FlashMessageType::SUCCESS, "Demande accepté " . $postulation->getUtilisateur()->getNom() . ", est maintenant bénévole");
        $this->em->flush();

        return $this->redirectToRoute('app_festival_postulations', ['id' => $festival->getId()]);
    }

    #[Route('postulations/{id}/refuse', name: 'app_postulations_refuse', methods: ['GET', 'POST'])]
    public function postulationRefuse(#[MapEntity] Postulations $postulation, Request $req,): Response {

        if ($postulation->getStatus() != 0) {
            $this->addFlash('error', 'Cette demande a déjà été traité');
            return $this->redirectToRoute('app_festival_detail', ['id' => $postulation->getFestival()->getId()]);
        }

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        $festival = $postulation->getFestival();

        if (!$this->utilisateurUtils->isOrganisateur($u, $festival)) {
            $this->addFlash('error', 'Vous ne pouvez pas effectué cette action');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        };

        if ($postulation->getStatus() !== Postulations::STATUS_PENDING) {
            $this->addFlash('error', 'Cette demande a déjà été traité');
            return $this->redirectToRoute('app_festival_postulations', ['id' => $festival->getId()]);
        };

        if ($req->isMethod('POST')) {
            $postulation->setStatus(-1);
            $formData = $req->request->all();
            $motif = $formData['motif'];

            $postulation->setMotif($motif);

            $this->flashMessageService->add(FlashMessageType::SUCCESS, "Demande refusé");
            $this->em->flush();
            return $this->redirectToRoute('app_festival_postulations', ['id' => $festival->getId()]);
        }

        return $this->render('postulations/refuse.html.twig', [
            'festival' => $festival,
            'postulation' => $postulation,
        ]);
    }
}
