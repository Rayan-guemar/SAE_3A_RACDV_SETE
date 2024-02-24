<?php

namespace App\Controller;

use App\Entity\Festival;
use App\Entity\Postulations;
use App\Entity\Reponse;
use App\Entity\Utilisateur;
use App\Repository\PostulationsRepository;
use App\Repository\QuestionBenevoleRepository;
use App\Service\FlashMessageService;
use App\Service\FlashMessageType;
use App\Service\UtilisateurUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('{_locale<%app.supported_locales%>}')]
class PostulationController extends AbstractController {
    #[Route('/festival/{id}/postulations', name: 'app_festival_postulations')]
    public function festivalPostulations(#[MapEntity] Festival $festival, PostulationsRepository $postulationRepository): Response {
        $postulations = $postulationRepository->findBy(['festival' => $festival, 'status' => Postulations::STATUS_PENDING]);
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
    public function postulation(#[MapEntity] Postulations $postulation, UtilisateurUtils $utilisateurUtils): Response {

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

        if (!($utilisateurUtils->isOrganisateur($u, $festival))) {
            $this->addFlash('error', 'Vous n\'êtes pas organisateur de ce festival');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        };

        $responses = $postulation->getReponses();

        return $this->render('postulations/postulation.html.twig', [
            'postulation' => $postulation,
            'responses' => $responses,
        ]);
    }

    #[Route('festival/{id}/postulations/form', name: 'app_postulations_form')]
    public function postulationForm(#[MapEntity] Festival $festival, Request $req, UtilisateurUtils $utilisateurUtils, QuestionBenevoleRepository $questionBenevoleRepository, EntityManagerInterface $em, PostulationsRepository $postulationRepository): Response {

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        if ($utilisateurUtils->isOrganisateur($u, $festival)) {
            $this->addFlash('error', 'Vous ne pouvez pas postuler à votre propre festival');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        };

        if ($utilisateurUtils->isBenevole($u, $festival)) {
            $this->addFlash('error', 'Vous participez déjà à ce festival');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        }

        $postulation =  $postulationRepository->findOneBy(['festival' => $festival, 'utilisateur' => $u]);

        if ($postulation) {
            $this->addFlash('error', 'Vous avez déjà postulé à ce festival');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        }


        if ($req->isMethod('POST')) {

            $postulation = new Postulations();
            $postulation->setFestival($festival);
            $postulation->setUtilisateur($u);
            $postulation->setDate(new \DateTime());
            $postulation->setStatus(Postulations::STATUS_PENDING);

            $formData = $req->request->all();

            foreach ($formData['responses'] as $questionId => $responseContent) {
                $response = new Reponse();
                $question = $questionBenevoleRepository->find($questionId);
                $response
                    ->setQuestion($question)
                    ->setContenue($responseContent);
                $response->setPostulation($postulation);
                $em->persist($response);
            }

            $em->persist($postulation);
            $em->flush();

            $this->addFlash('success', 'Votre postulation a bien été prise en compte');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        }

        $questions = $festival->getQuestionBenevoles();
        if ($questions->isEmpty()) {

            $postulation = new Postulations();
            $postulation->setFestival($festival);
            $postulation->setUtilisateur($u);
            $postulation->setDate(new \DateTime());
            $postulation->setStatus(Postulations::STATUS_PENDING);

            $em->persist($postulation);

            $this->addFlash('success', 'Votre postulation a bien été prise en compte');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        }

        return $this->render('postulations/form.html.twig', [
            'festival' => $festival,
            'questions' => $questions,
        ]);
    }

    #[Route('postulations/{id}/accept', name: 'app_postulations_accept', methods: ['POST'])]
    public function postulationAccept(#[MapEntity] Postulations $postulation, UtilisateurUtils $utilisateurUtils, EntityManagerInterface $em, FlashMessageService $flashMessageService): Response {
        $u = $this->getUser();

        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        $festival = $postulation->getFestival();

        if (!$utilisateurUtils->isOrganisateur($u, $festival)) {
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
        $em->persist($festival);

        $flashMessageService->add(FlashMessageType::SUCCESS, "Demande accepté " . $postulation->getUtilisateur() . ", est maintenant bénévole");
        $em->flush();

        return $this->redirectToRoute('app_festival_postulations', ['id' => $festival->getId()]);
    }

    #[Route('postulations/{id}/refuse', name: 'app_postulations_refuse', methods: ['GET', 'POST'])]
    public function postulationRefuse(#[MapEntity] Postulations $postulation, Request $req, UtilisateurUtils $utilisateurUtils, EntityManagerInterface $em, FlashMessageService $flashMessageService): Response {

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        $festival = $postulation->getFestival();

        if (!$utilisateurUtils->isOrganisateur($u, $festival)) {
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

            $flashMessageService->add(FlashMessageType::SUCCESS, "Demande refusé");
            $em->flush();
            return $this->redirectToRoute('app_festival_postulations', ['id' => $festival->getId()]);
        }

        return $this->render('postulations/refuse.html.twig', [
            'festival' => $festival,
            'postulation' => $postulation,
        ]);
    }
}
