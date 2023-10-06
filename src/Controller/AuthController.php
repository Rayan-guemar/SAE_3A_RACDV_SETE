<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;;

class AuthController extends AbstractController {


    #[Route('/auth/login', name: 'app_auth_login')]
    public function login(): Response {
        return $this->render('auth/login.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }

    #[Route('/auth/register', name: 'app_auth_register')]
    public function register(EntityManagerInterface $em, RequestStack $requestStack, Request $req, UserPasswordHasherInterface $passwordHasher): Response {
        $u = new Utilisateur();

        $f = $this->createForm(InscriptionType::class, $u, [
            'action' => $this->generateUrl('app_auth_register'),
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

            return $this->redirectToRoute('home');
        }

        return $this->render('auth/register.html.twig', [
            'controller_name' => 'UtilisateurController',
            'form' => $f->createView(),
        ]);
    }

    #[Route('/logout', name: 'app_auth_logout', methods: ['GET'])]
    public function logout(): never {
        throw new \Exception('This should never be reached!');
    }
}
