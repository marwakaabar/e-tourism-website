<?php

namespace App\Controller;

use App\Entity\Excursion;
use App\Entity\Images;
use App\Form\ExcursionType;
use App\Entity\GrilleTarifaire;
use App\Repository\ExcursionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ClientRepository;
use App\Form\GrilleTarifaireType;
use App\Repository\OffresRepository;
use App\Repository\GrilleTarifaireRepository;
use App\Entity\Reservation;
/**
 * @Route("/excursion")
 */
class ExcursionController extends AbstractController
{
    /**
     * @Route("/", name="app_excursion_index", methods={"GET"})
     */
    public function index(ExcursionRepository $excursionRepository): Response
    {
        $user=$this->getUser();
        $agence=$user->getAgence();
        return $this->render('excursion/index.html.twig', [
            'excursions' => $excursionRepository->findByAgence($agence),
            
        ]);
    }

    /**
     * @Route("/new", name="app_excursion_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ExcursionRepository $excursionRepository): Response
    {
        $excursion = new Excursion();
        $form = $this->createForm(ExcursionType::class, $excursion);
        $form->handleRequest($request);

    
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success','Ajout avec Success');
            // On récupère les images transmises
            $images = $form->get('images')->getData();

            // On boucle sur les images
            foreach ($images as $image) {
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                // On crée l'image dans la base de données
                $img = new Images();
                $img->setUrl($fichier);
                $img->setName($fichier);
                $excursion->addImage($img);
            }
            $agent=$this->getUser();
            $agence=$agent->getAgence();
            $excursion->setAgence($agence);
            $excursionRepository->add($excursion);
            return $this->redirectToRoute('app_excursion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('excursion/new.html.twig', [
            'excursion' => $excursion,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_excursion_show",  methods={"GET", "POST"})
     */
    public function show(Excursion $excursion,ClientRepository $clientRepository, Request $request,GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {   $clients= $clientRepository->findAll();
        $grilletarifaire = new Grilletarifaire();
        $formgrille = $this->createForm(GrilleTarifaireType::class, $grilletarifaire);
       
        $formgrille->handleRequest($request);
        $grilletarifaires= $excursion->getGrilletarifaires(); 
        if ($formgrille->isSubmitted() && $formgrille->isValid()) {
            $grilletarifaire->setOffre($excursion);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($grilletarifaire);
            $entityManager->flush();
          

            return $this->redirectToRoute('app_excursion_show', ['id' => $excursion->getId()] );
        }
       
        return $this->render('excursion/show.html.twig', [
            'grilletarifaires' => $grilletarifaires,
            'excursion' => $excursion,
            'clients'=>$clients,

            'formgrille' => $formgrille->createView(),
          
           
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_excursion_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Excursion $excursion, ExcursionRepository $excursionRepository): Response
    {
        $form = $this->createForm(ExcursionType::class, $excursion);
   

        $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $this->addFlash('success','Modifier avec Success');
                // On récupère les images transmises
                $images = $form->get('images')->getData();
    
                // On boucle sur les images
                foreach ($images as $image) {
                    // On génère un nouveau nom de fichier
                    $fichier = md5(uniqid()) . '.' . $image->guessExtension();
    
                    // On copie le fichier dans le dossier uploads
                    $image->move(
                        $this->getParameter('images_directory'),
                        $fichier
                    );
    
                    // On crée l'image dans la base de données
                    $img = new Images();
                    $img->setUrl($fichier);
                    $img->setName($fichier);
                    $excursion->addImage($img);
                }
                
            $excursionRepository->add($excursion);
            return $this->redirectToRoute('app_excursion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('excursion/edit.html.twig', [
            'excursion' => $excursion,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_excursion_delete", methods={"POST"})
     */
    public function delete(Request $request, Excursion $excursion, ExcursionRepository $excursionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$excursion->getId(), $request->request->get('_token'))) {
            $excursionRepository->remove($excursion);
        }

        return $this->redirectToRoute('app_excursion_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/reservation/offre", name="excursion_reservation", methods={"POST"})
     */
    public function reservation(Request $request,ClientRepository $clientRepository,GrilleTarifaireRepository $grilletarifaireRepository,OffresRepository $offreRepository): Response
    {   
          
         $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST') {
            $reservation = new Reservation();

        $idclient = $request->request->get('client');
        $idgrilletarifaire = $request->request->get('grilletarifaire');
        $idoffre = $request->request->get('offre');

        $client =  $clientRepository->find($idclient);
        $grilletarifaire =  $grilletarifaireRepository->find($idgrilletarifaire);
        $offre =  $offreRepository->find($idoffre);
            $reservation->setClient($client);
          $reservation->setGrilleTarifaire($grilletarifaire);
            $reservation->setOffre($offre);
        $reservation->setAgence($offre->getAgence());
           $reservation->setStatus('non_traitee');
        $reservation->setDateCreation(new \DateTime('now'));
       
            $em->persist($reservation);
            $em->flush();
          }
          return $this->redirectToRoute('app_reservation_index' );
    
        
        
    }
     /**
     * @Route("image/delete/{id}" , name="image_delete_excursion")
     */
    public function deleteImages($id)
    {
        $em = $this->getDoctrine()->getManager();
        $images = $this->getDoctrine()->getRepository(Images::class);
        $images = $images->find($id);
        $excursion = $images->getOffres($id);
        if (!$images) {
            throw $this->createNotFoundException(
                'There are no articles with the following id: ' . $id
            );
        }
        $em->remove($images);
        $em->flush();
        return $this->redirectToRoute('app_excursion_edit', ['id' => $excursion->getId()]);
    }

}