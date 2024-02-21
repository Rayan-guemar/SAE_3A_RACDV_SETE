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

class ContactsController extends AbstractController
{
    public function __construct(
        public FlashMessageService $flashMessageService, 
        public ContactRepository $contactRepository, 
        public TypeContactRepository $typeContactRepository,
        public EntityManagerInterface $em
        ) {}

        #[Route('/user/contacts', name: 'app_user_contacts', methods: ['GET'])]
        public function contacts(FlashMessageService $flashMessageService, TypeContactRepository $typeContactRepository): Response {
            $u = $this->getUser();
            if (!$u instanceof Utilisateur) {
                $flashMessageService->add(FlashMessageType::ERROR, "Vous n'êtes pas connecté");
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

    #[Route('/user/contacts/add', name: 'app_user_contacts_add', methods: ['POST'])]
    public function contactsAdd(Request $req,): Response {
        $u = $this->getUser();
        
        if (!$u instanceof Utilisateur) {
            $this->flashMessageService->add(FlashMessageType::ERROR, "L'utilisateur n'existe pas");
            return $this->redirectToRoute('home');
        }
        
        $value = $req->request->get('value');
        $type = $req->request->get('type');

        if (!$value || !$type) {
            $this->flashMessageService->add(FlashMessageType::ERROR, "Veuillez remplir tous les champs");
            return $this->redirectToRoute('app_user_contacts');
        }
        
        $typeContact = $this->typeContactRepository->findOneBy(['name' => $type]);
        if (!$typeContact) {
            $this->flashMessageService->add(FlashMessageType::ERROR, "Le type de contact n'existe pas");
            return $this->redirectToRoute('app_user_contacts');
        }

        $c = new Contact();
        $c->setUtilisateur($u);
        $c->setType($typeContact);
        $c->setValue($value);

        $this->em->persist($c);
        $this->em->flush();

        $this->flashMessageService->add(FlashMessageType::SUCCESS, "Contact ajouté avec succès");
        return $this->redirectToRoute('app_user_contacts');
    }


    #[Route('/user/contacts/{id}/delete', name: 'app_user_contacts_delete', methods: ['GET'])]
    public function contactsDelete(#[MapEntity] Contact $contact): Response {
        $u = $this->getUser();
        
        if (!$u instanceof Utilisateur) {
            $this->flashMessageService->add(FlashMessageType::ERROR, "L'utilisateur n'existe pas");
            return $this->redirectToRoute('home');
        }

        if ($contact->getUtilisateur() !== $u) {
            $this->flashMessageService->add(FlashMessageType::ERROR, "Vous n'avez pas le droit de supprimer ce contact");
            return $this->redirectToRoute('app_user_contacts');
        }

        $this->em->remove($contact);
        $this->em->flush();

        $this->flashMessageService->add(FlashMessageType::SUCCESS, "Contact supprimé avec succès");
        return $this->redirectToRoute('app_user_contacts');
    }
}
