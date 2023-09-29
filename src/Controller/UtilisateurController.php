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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UtilisateurController extends AbstractController {



    #[Route('/inscription', name: 'inscription', methods: ['GET', 'POST'])]
    public function inscription(EntityManagerInterface $entityManagerInterface, RequestStack $requestStack, Request $req): Response {
        $u = new Utilisateur();

        $f = $this->createForm(InscriptionType::class, $u, [
            'action' => $this->generateUrl('inscription'),
            'method' => 'POST',
        ]);

        $f->handleRequest($req);
        if ($f->isSubmitted()) {
            if (!$f->isValid()) {
                $errors = $f->getErrors(true);
                $flashBag = $requestStack->getSession()->getFlashBag();
                foreach ($errors as $error) {
                    $flashBag->add("error", $error->getMessage());
                }
            }

            $u->setMotDePasse($f->get('plain_mot_de_passe')->getData());

            $entityManagerInterface->persist($u);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_festival');
        }



        return $this->render('utilisateur/inscription.html.twig', [
            'controller_name' => 'UtilisateurController',
            'form' => $f->createView(),
        ]);
    }
}
