<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\FestivalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FestivalController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(FestivalRepository $repository, Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd($searchData);
//            $searchData->page = $request->query->getInt('page', 1);
//            $festivals = $repository->findBy(['nom' => ('%' . $searchData->q . '%')]);
//
//
//            return $this->render('festival/index.html.twig', [
//                'form' => $form->createView(),
//                'festivals' => $festivals
//            ]);
        }
        $festivals=$repository->findAll();
        return $this->render('festival/index.html.twig', [
            'form' => $form->createView(),
            'festivals' => $festivals
        ]);
    }
}
