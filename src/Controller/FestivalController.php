<?php

namespace App\Controller;

use App\Entity\Festival;
use App\Repository\FestivalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\DemandeFestival;
use App\Form\DemandeFestivalType;
use App\Repository\DemandeFestivalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class FestivalController extends AbstractController {


    #[Route('/', name: 'home')]
    public function index(): Response {
        return $this->redirectToRoute('app_festivall_all');
    }


    #[Route('/festival/all', name: 'app_festivall_all')]
    public function all(FestivalRepository $repository): Response {
        $festivals = $repository->findAll();
        return $this->render('festival/index.html.twig', [
            'controller_name' => 'FestivalController',
            'festivals' => $festivals
        ]);
    }

    #[Route('/festival/{id}', name: 'app_festival_show')]
    public function show(FestivalRepository $repository, int $id): Response {

        $festival = $repository->find($id);

        if (!$festival) {
            throw $this->createNotFoundException("Le festival n'existe pas");
        }

        return $this->render('festival/detailfest.html.twig', [
            'controller_name' => 'FestivalController',
            'festival' => $festival
        ]);
    }
}
