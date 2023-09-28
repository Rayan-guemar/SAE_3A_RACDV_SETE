<?php

namespace App\Controller;

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
}
