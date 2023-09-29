<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UtilisateurController extends AbstractController {


    #[Route('/inscription', name: 'inscription', methods: ['GET', 'POST'])]
    public function inscription(EntityManagerInterface $em, RequestStack $requestStack, Request $req, UserPasswordHasherInterface $passwordHasher ): Response {
        $u = new Utilisateur();

        $f = $this->createForm(InscriptionType::class, $u, [
            'action' => $this->generateUrl('inscription'),
            'method' => 'POST',
        ]);

        $f->handleRequest($req);
        if ($f->isSubmitted() && $f->isValid()) {
            if (!$f->isValid()) {
                $errors = $f->getErrors(true);
                $flashBag = $requestStack->getSession()->getFlashBag();
                foreach ($errors as $error) {
                    $flashBag->add("error", $error->getMessage());
                }
            }

            $plainTextPassword = $f->get('plain_mot_de_passe')->getData();
            $hashedPassword = $passwordHasher->hashPassword($u, $plainTextPassword);
            $u->setPassword($hashedPassword);

            $em->persist($u);
            $em->flush();

            return $this->redirectToRoute('app_festival');
        }


        return $this->render('utilisateur/inscription.html.twig', [
            'controller_name' => 'UtilisateurController',
            'form' => $f->createView(),
        ]);
    }


    #[Route('/connexion', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(): Response
    {
        return $this->render('utilisateur/login.html.twig');
    }
    
}
