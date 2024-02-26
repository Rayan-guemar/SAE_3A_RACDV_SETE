<?php

namespace App\Controller;

use App\Entity\Validation;
use App\Repository\ValidationRepository;
use App\Entity\Festival;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use App\Service\FlashMessageService;
use App\Service\FlashMessageType;
use App\Service\UtilisateurUtils;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('{_locale<%app.supported_locales%>}')]
class ValidationController extends AbstractController {

    /**
     * Ajoute une demande de validation pour le festival {id}
     */
    #[Route('festival/{id}/validation', name: 'app_festival_validation')]
    public function addFestivalValidation(#[MapEntity] Festival $festival, Request $req, EntityManagerInterface $em, FlashMessageService $flashMessageService, UtilisateurUtils $utilisateurUtils, ValidationRepository $validationRepository, TranslatorInterface $translator): Response {

        if (!$festival) {
            throw $this->createNotFoundException($translator->trans('festival.error.notFound', [], 'msgflash', $translator->getLocale()));
        }

        $u = $this->getUser();
        if (!$u || !$u instanceof Utilisateur) {
            $this->addFlash('error', $translator->trans('user.error.notConnected', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_auth_login');
        }

        if (!($utilisateurUtils->isOrganisateur($u, $festival) || $utilisateurUtils->isResponsable($u, $festival) || $utilisateurUtils->isBenevole($u, $festival))) {
            $this->addFlash('error', $translator->trans('user.error.permissionDenied', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('home');
        }


        if ($festival->getValid() == 1) {
            $flashMessageService->add(FlashMessageType::ERROR, $translator->trans('festival.error.alreadyValid', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_gestion', ['id' => $festival->getId()]);
        }

        if ($req->getMethod() === 'GET') {
            // TODO: ajouter la page
            $validations = $festival->getValidations();
            return $this->render('festival/validations.html.twig', [
                'festival' => $festival,
                'validations' => $validations,
            ]);
        }

        $enAttente = $validationRepository->findBy(['festival' => $festival, 'status' => 0]);

        if ($enAttente != null) {
            $flashMessageService->add(FlashMessageType::ERROR, $translator->trans('festival.error.pending', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_festival_gestion', ['id' => $festival->getId()]);
        }

        $v = new Validation();
        $v->setFestival($festival);

        $em->persist($v);
        $em->flush();

        $flashMessageService->add(FlashMessageType::SUCCESS, $translator->trans('festival.error.validationSent', [], 'msgflash', $translator->getLocale()));

        return $this->redirectToRoute('app_festival_gestion', ['id' => $festival->getId()]);
    }

    /**
     * Affiche les demandes de validation en attente
     */
    //#[IsGranted("ROLE_ADMIN")]
    #[Route('/validation', name: 'app_validation', methods: 'GET')]
    public function getPendingValidations(ValidationRepository $validationRepository): Response {
        $validations = $validationRepository->findBy([
            'status' => 0
        ]);

        return $this->render('demande_festival/index.html.twig', [
            'validations' => $validations,
        ]);
    }

    /**
     * Affiche l'historique des demandes de validation
     */
    // #[IsGranted("ROLE_ADMIN")]
    #[Route('/validation/history', name: 'app_validation_history', methods: 'GET')]
    public function getValidationsHistory(ValidationRepository $validationRepository): Response {
        $validations = $validationRepository->createQueryBuilder('v')
            ->where('v.status != 0')
            ->getQuery()
            ->getResult();

        // TODO: ajouter la page
        return $this->render('validation/index.html.twig', [
            'validations' => $validations,
        ]);
    }

    /**
     * Accepte la demande de validation du festival
     */
    //#[IsGranted("ROLE_ADMIN")]
    #[Route('/validation/{id}/accept', name: 'app_validation_accept', methods: 'POST')]
    public function accept(#[MapEntity] Validation $validation, EntityManagerInterface $em, FlashMessageService $flashMessageService, TranslatorInterface $translator): Response {

        if (!$validation) {
            throw $this->createNotFoundException($translator->trans('demande.error.notFound', [], 'msgflash', $translator->getLocale()));
        }

        if ($validation->getStatus() != 0) {
            throw new BadRequestException($translator->trans('demande.error.notPending', [], 'msgflash', $translator->getLocale()));
        }

        $validation->accept();

        $em->persist($validation);
        $em->flush();

        $flashMessageService->add(FlashMessageType::SUCCESS, $translator->trans('festival.success.validationAccepted', [], 'msgflash', $translator->getLocale()));

        return $this->redirectToRoute('app_validation');
    }

    /**
     * Rejette la demande de validation du festival
     */
    // #[IsGranted("ROLE_ADMIN")]
    #[Route('/validation/{id}/reject', name: 'app_validation_reject', methods: 'POST')]
    public function reject(#[MapEntity] Validation $validation, EntityManagerInterface $em, FlashMessageService $flashMessageService, Request $req, TranslatorInterface $translator): Response {

        if (!$validation) {
            throw $this->createNotFoundException($translator->trans('demande.error.notFound', [], 'msgflash', $translator->getLocale()));
        }

        if ($validation->getStatus() != 0) {
            throw new BadRequestException($translator->trans('demande.error.notPending', [], 'msgflash', $translator->getLocale()));
        }

        if (!$req->request->get('motif')) {
            throw new BadRequestException($translator->trans('motif.error.void', [], 'msgflash', $translator->getLocale()));
            $this->addFlash('error', $translator->trans('motif.error.void', [], 'msgflash', $translator->getLocale()));
            return $this->redirectToRoute('app_validation');
        }

        $validation->setMotif($req->request->get('motif'));
        $validation->reject();

        $em->persist($validation);
        $em->flush();

        $flashMessageService->add(FlashMessageType::SUCCESS, $translator->trans('festival.success.validationRefused', [], 'msgflash', $translator->getLocale()));

        return $this->redirectToRoute('app_validation');
    }
}
