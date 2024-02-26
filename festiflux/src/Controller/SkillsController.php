<?php

namespace App\Controller;

use App\Repository\SkillsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FlashMessageService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Utilisateur;
use App\Entity\Skills;
use App\Service\FlashMessageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;




class SkillsController extends AbstractController
{

    public function __construct(
        public FlashMessageService $flashMessageService, 
        public SkillsRepository $skillsRepository, 
        public EntityManagerInterface $em
        ) {}


    #[Route('/user/skills', name: 'app_user_skills', methods: ['GET'])]
    public function skills(FlashMessageService $flashMessageService): Response {
        $u = $this->getUser();
        if (!$u instanceof Utilisateur) {
            $flashMessageService->add(FlashMessageType::ERROR, "Vous n'êtes pas connecté");
            return $this->redirectToRoute('home');
        }

        return $this->render('contacts/contacts.html.twig', [
            'controller_name' => 'UtilisateurController',
            'utilisateur' => $u,
            'skills' => $u->getSkills(),
        ]);
    }

    #[Route('/user/skills/add', name: 'app_user_skills_add', methods: ['POST'])]
    public function skillsAdd(Request $req,): Response {
        $u = $this->getUser();
        
        if (!$u instanceof Utilisateur) {
            $this->flashMessageService->add(FlashMessageType::ERROR, "L'utilisateur n'existe pas");
            return $this->redirectToRoute('home');
        }
        
        $value = $req->request->get('value');
        $level = $req->request->get('level');

        if (!$value || !$level) {
            $this->flashMessageService->add(FlashMessageType::ERROR, "Veuillez remplir tous les champs");
            return $this->redirectToRoute('app_user_skills');
        }
        
        $skill = new Skills();
        $skill->setUser($u);
        $skill->setName($value);
        $skill->setLevel($level);

        $this->em->persist($skill);
        $this->em->flush();

        $this->flashMessageService->add(FlashMessageType::SUCCESS, "Compétence ajoutée avec succès");
        return $this->redirectToRoute('app_user_skills');
    }


    #[Route('/user/skills/{id}/delete', name: 'app_user_contacts_delete', methods: ['GET'])]
    public function contactsDelete(#[MapEntity] Skills $skill): Response {
        $u = $this->getUser();
        
        if (!$u instanceof Utilisateur) {
            $this->flashMessageService->add(FlashMessageType::ERROR, "L'utilisateur n'existe pas");
            return $this->redirectToRoute('home');
        }

        if ($skill->getUser() !== $u) {
            $this->flashMessageService->add(FlashMessageType::ERROR, "Vous n'avez pas le droit de supprimer cette compétence");
            return $this->redirectToRoute('app_user_skills');
        }

        $this->em->remove($skill);
        $this->em->flush();

        $this->flashMessageService->add(FlashMessageType::SUCCESS, "Contact supprimé avec succès");
        return $this->redirectToRoute('app_user_skills');
    }
}
