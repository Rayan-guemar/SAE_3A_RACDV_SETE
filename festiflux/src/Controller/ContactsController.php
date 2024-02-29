<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Utilisateur;
use App\Repository\ContactRepository;
use App\Repository\TypeContactRepository;
use App\Service\FlashMessageService;
use App\Service\FlashMessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactsController extends AbstractController
{
    public function __construct(
        public FlashMessageService    $flashMessageService,
        public ContactRepository      $contactRepository,
        public TypeContactRepository  $typeContactRepository,
        public EntityManagerInterface $em,
        public TranslatorInterface    $translator
    )
    {
    }

    /**
     * affiche la liste des contacts de l'utilisateur
     */
    #[Route('/user/contacts', name: 'app_user_contacts', methods: ['GET'])]
    public function contacts(FlashMessageService $flashMessageService, TypeContactRepository $typeContactRepository): Response
    {
        $u = $this->getUser();
        if (!$u instanceof Utilisateur) {
            $flashMessageService->add(FlashMessageType::ERROR, $this->translator->trans('user.error.notConnected', [], 'msgflash', $this->translator->getLocale()));
            return $this->redirectToRoute('home');
        }

        $typeContacts = $typeContactRepository->findAll();

        return $this->render('contacts/contacts.html.twig', [
            'controller_name' => 'UtilisateurController',
            'utilisateur' => $u,
            'contacts' => $u->getContacts(),
            'typeContacts' => $typeContacts,
        ]);
    }

    /**
     * Ajoute un contact à l'utilisateur
     */
    #[Route('/user/contacts/add', name: 'app_user_contacts_add', methods: ['POST'])]
    public function contactsAdd(Request $req,): Response
    {
        $u = $this->getUser();

        if (!$u instanceof Utilisateur) {
            $this->flashMessageService->add(FlashMessageType::ERROR, $this->translator->trans('user.error.notFound', [], 'msgflash', $this->translator->getLocale()));
            return $this->redirectToRoute('home');
        }

        $value = $req->request->get('value');
        $type = $req->request->get('type');

        if (!$value || !$type) {
            $this->flashMessageService->add(FlashMessageType::ERROR, $this->translator->trans('field.error.notComplete', [], 'msgflash', $this->translator->getLocale()));
            return $this->redirectToRoute('app_user_contacts');
        }

        $typeContact = $this->typeContactRepository->findOneBy(['name' => $type]);
        if (!$typeContact) {
            $this->flashMessageService->add(FlashMessageType::ERROR, $this->translator->trans('field.error.notFound', [], 'msgflash', $this->translator->getLocale()));
            return $this->redirectToRoute('app_user_contacts');
        }

        $c = new Contact();
        $c->setUtilisateur($u);
        $c->setType($typeContact);
        $c->setValue($value);

        $this->em->persist($c);
        $this->em->flush();

        $this->flashMessageService->add(FlashMessageType::SUCCESS, $this->translator->trans('contact.success.add', [], 'msgflash', $this->translator->getLocale()));
        return $this->redirectToRoute('app_user_contacts');
    }

    /**
     * Supprime un contact de l'utilisateur
     */
    #[Route('/user/contacts/{id}/delete', name: 'app_user_contacts_delete', methods: ['GET'])]
    public function contactsDelete(#[MapEntity] Contact $contact): Response
    {
        $u = $this->getUser();

        if (!$u instanceof Utilisateur) {
            $this->flashMessageService->add(FlashMessageType::ERROR, $this->translator->trans('user.error.notFound', [], 'msgflash', $this->translator->getLocale()));
            return $this->redirectToRoute('home');
        }

        if ($contact->getUtilisateur() !== $u) {
            $this->flashMessageService->add(FlashMessageType::ERROR, $this->translator->trans('user.error.permissionDenied', [], 'msgflash', $this->translator->getLocale()));
            return $this->redirectToRoute('app_user_contacts');
        }

        $this->em->remove($contact);
        $this->em->flush();

        $this->flashMessageService->add(FlashMessageType::SUCCESS, $this->translator->trans('contact.success.deleted', [], 'msgflash', $this->translator->getLocale()));
        return $this->redirectToRoute('app_user_contacts');
    }
}
