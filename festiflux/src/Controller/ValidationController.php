<?php

namespace App\Controller;

use App\Entity\Validation;
use App\Repository\ValidationRepository;
use App\Entity\DemandeFestival;
use App\Entity\Festival;
use App\Entity\Tag;
use App\Entity\Utilisateur;
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
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use App\Service\FlashMessageService;
use App\Service\FlashMessageType;
use App\Service\UtilisateurUtils;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ValidationController extends AbstractController {
    #[Route('festival/{id}/validation', name: 'app_festival_validation_add', methods: ['POST', 'GET'])]
    public function addFestivalValidation(#[MapEntity] Festival $festival, Request $req, EntityManagerInterface $em, FlashMessageService $flashMessageService, UtilisateurUtils $utilisateurUtils): Response {

        if (!$festival) {
            throw $this->createNotFoundException("Le festival n'existe pas");
        }

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_auth_login');
        }

        if (!($utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival) || $utilisateurUtils->isBenevole($u, $festival))) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette page');
            return $this->redirectToRoute('home');
        }

        $validations = $festival->getValidations();


        if ($req->getMethod() === 'GET') {
            // TODO: ajouter la page
            return $this->render('festival/validations.html.twig', [
                'festival' => $festival,
                'validations' => $validations,
            ]);
        } else {

            if ($validations->filter(function (Validation $v) {
                $v->getStatus() == 0;
            })->count() > 0) {
                $flashMessageService->add(FlashMessageType::ERROR, "Une demande de validation est déjà en cours");
                return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
            }

            $v = new Validation();
            $v->setFestival($festival);

            $em->persist($v);
            $em->flush();

            $flashMessageService->add(FlashMessageType::SUCCESS, "Votre Demande de validation à bien été envoyé");

            return $this->redirectToRoute('app_festival_detail', ['id' => $festival->getId()]);
        }
    }

    // #[IsGranted("ROLE_ADMIN")]
    #[Route('/validation', name: 'app_validation', methods: 'GET')]
    public function getPendingValidations(ValidationRepository $validationRepository): Response {
        $validations = $validationRepository->findBy([
            'status' => 0
        ]);

        return $this->render('demande_festival/index.html.twig', [
            'validations' => $validations,
        ]);
    }

    // #[IsGranted("ROLE_ADMIN")]
    #[Route('/validation/history', name: 'app_validation_history', methods: 'GET')]
    public function getValidationsHistory(ValidationRepository $validationRepository): Response {
        $validations = $validationRepository->createQueryBuilder('v')
            ->where('v.status != 0')
            ->getQuery()
            ->getResult();

        // TODO: ajouter la page
        // return $this->render('validation/index.html.twig', [
        //     'validations' => $validations,
        // ]);

        return new JsonResponse([
            'validations' => array_map(function (Validation $v) {
                return [
                    'id' => $v->getId(),
                    'status' => $v->getStatus(),
                    'festival' => $v->getFestival()->getId(),
                ];
            }, $validations),
        ]);
    }

    //#[IsGranted("ROLE_ADMIN")]
    #[Route('/validation/{id}/accept', name: 'app_validation_accept', methods: 'POST')]
    public function accept(#[MapEntity] Validation $validation, EntityManagerInterface $em, FlashMessageService $flashMessageService): Response {

        if (!$validation) {
            throw $this->createNotFoundException("La demande de validation n'existe pas");
        }

        if ($validation->getStatus() != 0) {
            throw new BadRequestException("La demande de validation n'est pas en attente");
        }

        $validation->accept();

        $em->persist($validation);
        $em->flush();

        $flashMessageService->add(FlashMessageType::SUCCESS, "La validation à bien été accepté");

        return $this->redirectToRoute('app_validation');
    }

    // #[IsGranted("ROLE_ADMIN")]
    #[Route('/validation/{id}/reject', name: 'app_validation_reject', methods: 'POST')]
    public function reject(#[MapEntity] Validation $validation, EntityManagerInterface $em, FlashMessageService $flashMessageService): Response {

        if (!$validation) {
            throw $this->createNotFoundException("La demande de validation n'existe pas");
        }

        if ($validation->getStatus() != 0) {
            throw new BadRequestException("La demande de validation n'est pas en attente");
        }

        $validation->reject();

        $em->persist($validation);
        $em->flush();

        $flashMessageService->add(FlashMessageType::SUCCESS, "La validation à bien été rejeté");

        return $this->redirectToRoute('app_validation');
    }
}
