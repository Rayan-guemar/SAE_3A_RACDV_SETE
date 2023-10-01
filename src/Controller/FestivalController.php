<?php

namespace App\Controller;

use App\Repository\FestivalRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FestivalController extends AbstractController {
    #[Route('/', name: 'app_festival')]
    public function index(): Response {
        return $this->render('festival/index.html.twig', [
            'controller_name' => 'FestivalController',
        ]);
    }

    #[Route('/festival/{id}', name: 'app_festival')]
    public function festival(int $id, FestivalRepository $festivalRepository): Response {

        $f = $festivalRepository->findOneBy(['id' => $id]);

        if (!$f) {
            throw $this->createNotFoundException('Festival non trouvé');
        }

        return $this->render('festival/festival.html.twig', [
            'controller_name' => 'FestivalController',
            'festival' => $f,
        ]);
    }
}
