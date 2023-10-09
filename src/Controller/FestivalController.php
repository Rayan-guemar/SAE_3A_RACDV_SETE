<?php

namespace App\Controller;

use App\Entity\Festival;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\FestivalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\DemandeFestival;
use App\Form\DemandeFestivalType;
use App\Repository\DemandeFestivalRepository;
use Doctrine\ORM\EntityManagerInterface;


class FestivalController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(FestivalRepository $repository): Response
    {
        return $this->redirectToRoute('app_festival_all');
    }


    #[Route('/festival/all', name: 'app_festival_all')]
    public function all(FestivalRepository $repository, Request $request): Response {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $festivals = $repository->findBySearch($searchData);

            return $this->render('festival/index.html.twig', [
                'form' => $form->createView(),
                'festivals' => $festivals
            ]);
        }
        $festivals=$repository->findAll();
        return $this->render('festival/index.html.twig', [
            'form' => $form->createView(),
            'festivals' => $festivals
        ]);
    }

    #[Route('/festival/{id}', name: 'app_festival_detail')]
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
