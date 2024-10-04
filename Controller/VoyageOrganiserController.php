<?php

namespace App\Controller;
use App\Entity\Images;
use App\Repository\ExcursionRepository;

use App\Entity\VoyageOrganiser;
use App\Entity\Reservation;
use App\Entity\GrilleTarifaire;
use App\Form\GrilleTarifaireType;

use App\Form\VoyageAgentType;
use App\Repository\ClientRepository;
use App\Repository\OffresRepository;
use App\Repository\VoyageOrganiserRepository;
use App\Repository\GrilleTarifaireRepository;
use App\Repository\VoyageExcursionRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/voyage/organiser")
 */
class VoyageOrganiserController extends AbstractController
{
    /**
     * @Route("/", name="app_voyage_organiser_index", methods={"GET"})
     */
    public function index(VoyageOrganiserRepository $voyageOrganiserRepository, GrilleTarifaireRepository $grilleTarifaireRepository, ExcursionRepository $excursionRepository,VoyageExcursionRepository $voyageExcursionRepository): Response
    {
        $user=$this->getUser();
        $agence=$user->getAgence();
        return $this->render('voyage_organiser/index.html.twig', [
            'voyage_organisers' => $voyageOrganiserRepository->findByAgence($agence),
            'grille_tarifaires' => $grilleTarifaireRepository->findAll(),
            //'grille_tarifaires' => $grilleTarifaireRepository->findByOffre('voyage_organisers'),
            //'excursions' => $excursionRepository->findAll(),    
            'voyage_excursions'=>$voyageExcursionRepository->findAll(),


            
         
          
            
           
        ]);
    }

    /**
     * @Route("/new", name="app_voyage_organiser_new", methods={"GET", "POST"})
     */
    public function new(Request $request, VoyageOrganiserRepository $voyageOrganiserRepository): Response
    {
        $voyageOrganiser = new VoyageOrganiser();
        $form = $this->createForm(VoyageAgentType::class, $voyageOrganiser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success','Ajout avec Success');
            $images = $form->get('images')->getData();
    
            // On boucle sur les images
            foreach($images as $image){
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                
                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                
                // On crée l'image dans la base de données
                $img = new Images();
                $img->setName($fichier);
                $img->setUrl($fichier);
                $voyageOrganiser->addImage($img);
            }
            $agent=$this->getUser();
            $agence=$agent->getAgence();
            $voyageOrganiser->setAgence($agence);
            $voyageOrganiserRepository->add($voyageOrganiser);
            return $this->redirectToRoute('app_voyage_organiser_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voyage_organiser/new.html.twig', [
            'voyage_organiser' => $voyageOrganiser,
            'form' => $form,
           
        ]);
    }
     /**
     * @Route("/reservation/offre", name="voyage_organiser_reservation", methods={"POST"})
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
     * @Route("/{id}", name="app_voyage_organiser_show", methods={"GET", "POST"})
     */
    public function show(VoyageOrganiser $voyageOrganiser ,ClientRepository $clientRepository, Request $request,GrilleTarifaireRepository $grilleTarifaireRepository,VoyageExcursionRepository $voyageExcursionRepository): Response
    {
        $clients= $clientRepository->findAll();
        $grilletarifaire = new Grilletarifaire();
        $formgrille = $this->createForm(GrilleTarifaireType::class, $grilletarifaire);
        $formgrille->handleRequest($request);
        $grilletarifaires= $voyageOrganiser->getGrilletarifaires(); 
        if ($formgrille->isSubmitted() && $formgrille->isValid()) {
            $grilletarifaire->setOffre($voyageOrganiser);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($grilletarifaire);
            $entityManager->flush();
          

            return $this->redirectToRoute('app_voyage_organiser_show', ['id' => $voyageOrganiser->getId()] );
        }
       
       

        return $this->render('voyage_organiser/show.html.twig', [
            'grilletarifaires' => $grilletarifaires,
            'voyage_organiser' => $voyageOrganiser,
            'clients'=>$clients,

          
            'formgrille' => $formgrille->createView(),
     
            'voyage_excursions'=>$voyageExcursionRepository->findByExcursion($voyageOrganiser),
            //'excursions' => $voyageExcursionRepository->findByVoyageExcursion($voyageOrganiser),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_voyage_organiser_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, VoyageOrganiser $voyageOrganiser, VoyageOrganiserRepository $voyageOrganiserRepository): Response
    {
        $form = $this->createForm(VoyageAgentType::class, $voyageOrganiser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success','Modifier avec Success');
            $images = $form->get('images')->getData();
    
            // On boucle sur les images
            foreach($images as $image){
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                
                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                
                // On crée l'image dans la base de données
                $img = new Images();
                $img->setName($fichier);
                $img->setUrl($fichier);
                $voyageOrganiser->addImage($img);
            }

            $voyageOrganiserRepository->add($voyageOrganiser);
            return $this->redirectToRoute('app_voyage_organiser_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voyage_organiser/edit.html.twig', [
            'voyage_organiser' => $voyageOrganiser,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_voyage_organiser_delete", methods={"POST"})
     */
    public function delete(Request $request, VoyageOrganiser $voyageOrganiser, VoyageOrganiserRepository $voyageOrganiserRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$voyageOrganiser->getId(), $request->request->get('_token'))) {
            $voyageOrganiserRepository->remove($voyageOrganiser);
        }

        return $this->redirectToRoute('app_voyage_organiser_index', [], Response::HTTP_SEE_OTHER);
    }
}