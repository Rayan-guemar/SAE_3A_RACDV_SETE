<?php

namespace App\Controller;

use App\Entity\Festival;
use App\Repository\FestivalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\DemandeFestival;
use App\Form\DemandeFestivalType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class FestivalController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(FestivalRepository $repository): Response
    {
        $festivals=$repository->findAll();
        return $this->render('festival/index.html.twig', [
            'controller_name' => 'FestivalController',
            'festivals' => $festivals
        ]);
    }

    #[Route('/festival/{id}', name: 'detailfest', methods: ["GET"])]
    public function detail(#[MapEntity] ?Festival $fest, FestivalRepository $repository ): Response
    {
        if($fest == null) {
            $this->addFlash('error','festival inexistant');
            return $this->redirectToRoute('accueil');
        }

        return $this->render('festival/detailfest.html.twig',[
            'festival'=>$fest
        ]);
    }

    #[Route('/festival/ask', name: 'festival_ask')]
    public function ask(Request $req, EntityManagerInterface $em ): Response
    {
        $demandeFestival = new DemandeFestival();

        $form = $this->createForm(DemandeFestivalType::class, $demandeFestival);

        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){
            $demandeFestival->setOrganisateurFestival($this->getUser());
            $em->persist($demandeFestival);
            $em->flush();
            return $this->redirectToRoute('accueil');
        }

        return $this->render('festival/demandeFestival.html.twig', [
            'controller_name' => 'FestivalController',
            'form' => $form->createView()
        ]);
    }
}
