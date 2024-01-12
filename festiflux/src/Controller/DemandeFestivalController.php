<?php
//
//namespace App\Controller;
//
//use App\Entity\DemandeFestival;
//use App\Entity\Festival;
//use App\Entity\Tag;
//use App\Form\FestivalType;
//use App\Repository\DemandeFestivalRepository;
//use App\Repository\FestivalRepository;
//use App\Repository\TagRepository;
//use Doctrine\ORM\EntityManagerInterface;
//use PHPUnit\Util\Json;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\String\Slugger\SluggerInterface;
//use Symfony\Component\HttpFoundation\File\Exception\FileException;
//use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\Security\Http\Attribute\IsGranted;
//
//class DemandeFestivalController extends AbstractController {
//
//    #[Route('/demandefestival/add', name: 'app_demandefestival_add', methods: ['GET', 'POST'])]
//    public function add(TagRepository $tagRepository, Request $req, EntityManagerInterface $em, SluggerInterface $slugger) {
//        $festivals = new Festival();
//        $form = $this->createForm(FestivalType::class, $festivals);
//        $form->handleRequest($req);
//        if($req->isMethod('POST')) {
//            $this->denyAccessUnlessGranted('ROLE_USER');
//            if ($form->isSubmitted() && $form->isValid()) {
//                if ($form->get('dateFin')->getData() > $form->get('dateDebut')->getData()) {
//                    $affiche = $form->get('affiche')->getData();
//                    if ($affiche) {
//                        $originalFilename = pathinfo("", PATHINFO_FILENAME);
//                        $safeFilename = $slugger->slug($originalFilename);
//                        $newFilename = $safeFilename . '-' . uniqid() . '.' . $affiche->guessExtension();
//                        try {
//                            $affiche->move(
//                                $this->getParameter('poster_directory'),
//                                $newFilename
//                            );
//                        } catch (FileException $e) {
//                            throw new \Exception('Erreur lors de l\'upload de l\'affiche');
//                        }
//                        $festivals->setAffiche($newFilename);
//                    }
//
//                    $festivals->setNom($form->get('nom')->getData());
//                    $festivals->setDateDebut($form->get('dateDebut')->getData());
//                    $festivals->setDateFin($form->get('dateFin')->getData());
//                    $festivals->setDescription($form->get('description')->getData());
//                    $festivals->setOrganisateur($this->getUser());
//                    $festivals->setLieu($form->get('lieu')->getData());
//                    $festivals->setLat($form->get('lat')->getData());
//                    $festivals->setLon($form->get('lon')->getData());
//
//                    $tag_arr = explode (",", $form->get('tags')->getData());
//                    foreach ($tag_arr as $tagName){
//                        $verif = ($tagRepository)->findBy(["nom"=>$tagName]);
//                        ($verif!= null)? $tag = $verif[0] : $tag = new Tag($tagName); $em->persist($tag);
//                        $festivals->addTag($tag);
//                    }
//
//                    $em->persist($festivals);
//                    $em->flush();
//
//                    $this->addFlash('success', 'Demande de festival acceptée');
//                    return $this->redirectToRoute('app_validation');
//                }
//                else {
//                    $this->addFlash('error', 'Les dates de votre festival ne sont pas conforme !');
//                    return $this->redirectToRoute('home');
//                }
//            }else{
//                $this->addFlash('error', 'une erreur est survenue lors de la soumission du formulaire !');
//                return $this->redirectToRoute('home');
//            }
//        }
//        return $this->render('demande_festival/add.html.twig', [
//            'controller_name' => 'FestivalController',
//            'form' => $form->createView()
//        ]);
//    }
//
//}
