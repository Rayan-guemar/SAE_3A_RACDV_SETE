<?php

namespace App\Controller;

use App\Entity\Festival;
use App\Entity\Postulations;
use App\Entity\Reponse;
use App\Entity\Utilisateur;
use App\Repository\PostulationsRepository;
use App\Repository\QuestionBenevoleRepository;
use App\Service\UtilisateurUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

        $festival = $postulation->getFestival();
        if (!($utilisateurUtils->isOrganisateur($u, $festival))) {
            $this->addFlash('error', 'Vous n\'êtes pas organisateur de ce festival');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        };

        return $this->render('postulations/postulation.html.twig', [
            'postulation' => $postulation,
        ]);
    }

    #[Route('festival/{id}/postulations/form', name: 'app_postulations_form')]
    public function postulationForm(#[MapEntity] Festival $festival, Request $req, UtilisateurUtils $utilisateurUtils, QuestionBenevoleRepository $questionBenevoleRepository, EntityManagerInterface $em): Response {

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }
        // if ($utilisateurUtils->isOrganisateur($u, $festival)) {
        //     $this->addFlash('error', 'Vous ne pouvez pas postuler à votre propre festival');
        //     return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        // };

        // if ($utilisateurUtils->isBenevole($u, $festival)) {
        //     $this->addFlash('error', 'Vous avez déjà postulé à ce festival');
        //     return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        // }

        if ($req->isMethod('POST')) {

            $postulation = new Postulations();
            $postulation->setFestival($festival);
            $postulation->setUtilisateur($u);
            $postulation->setDate(new \DateTime());
            $postulation->setStatus(Postulations::STATUS_PENDING);

            $em->persist($postulation);

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

            $em->flush();

            $this->addFlash('success', 'Votre postulation a bien été prise en compte');
            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        }

        $question = $festival->getQuestionBenevoles();

        return $this->render('postulations/form.html.twig', [
            'festival' => $festival,
            'questions' => $question,
        ]);
    }
}
