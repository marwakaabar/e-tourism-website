<?php

namespace App\Controller;
use App\Entity\Croisiere;
use App\Form\CroisiereType;
use App\Entity\Images;
use App\Repository\OffresRepository;
use App\Repository\CroisiereRepository;
use App\Form\GrilleTarifaireType;

use App\Entity\CroisiereExcursion;
use App\Form\CroisiereExcursionType;
use App\Entity\Agence;
use App\Entity\GrilleTarifaire;
use App\Repository\CroisiereExcursionRepository;
use App\Repository\ClientRepository;
use App\Entity\Reservation;
use App\Repository\AgenceRepository;
use App\Repository\GrilleTarifaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/croisiere/user")
 */
class CroisiereUserController extends AbstractController
{
     /**
     * @Route("/index", name="app_croisiere_user_index", methods={"GET"})
     */
    public function index(CroisiereRepository $croisiereRepository): Response
    {

           
     
        return $this->render('croisiere_user/index.html.twig', [
            'croisieres' => $croisiereRepository->findAll(),
           
            //'grille_tarifaires' => $grilleTarifaireRepository->findAll(),
            //'croisiere_excursions' => $croisiereExcursionRepository->findAll(),
            
            
            //'excursions' => $excursionRepository->findAll(),

        ]);
    }

    /**
     * @Route("/new", name="app_croisiere_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CroisiereRepository $croisiereRepository): Response
    {
        $croisiere = new Croisiere();
        $form = $this->createForm(CroisiereType::class, $croisiere);
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
                $croisiere->addImage($img);
            }
           
            $croisiereRepository->add($croisiere);
            return $this->redirectToRoute('app_croisiere_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('croisiere_user/new.html.twig', [
            'croisiere' => $croisiere,
            'form' => $form,
        ]);
    }
/**
     * @Route("/reservation/offre", name="croisiere_reservation", methods={"POST"})
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
     * @Route("/{id}", name="app_croisiere_user_show", methods={"GET", "POST"})
     */
    public function show(Croisiere $croisiere,ClientRepository $clientRepository, Request $request,GrilleTarifaireRepository $grilleTarifaireRepository, CroisiereExcursionRepository $croisiereExcursionRepository,OffresRepository $OffresRepository): Response
    {
        $croisiereexcursion=$croisiereExcursionRepository->findAll();
        $clients= $clientRepository->findAll();
        $grilletarifaire = new Grilletarifaire();
        $formgrille = $this->createForm(GrilleTarifaireType::class, $grilletarifaire);
        $formgrille->handleRequest($request);
        $grilletarifaires= $croisiere->getGrilletarifaires(); 
        if ($formgrille->isSubmitted() && $formgrille->isValid()) {
            $grilletarifaire->setOffre($croisiere);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($grilletarifaire);
            $entityManager->flush();
          

            return $this->redirectToRoute('app_croisiere_user_show', ['id' => $croisiere->getId()] );
        }
       
       
       
        return $this->render('croisiere_user/show.html.twig', [
            'grilletarifaires' => $grilletarifaires,
            'croisiere' => $croisiere,
            'clients'=>$clients,
            "croisiere_excursions"=>$croisiereexcursion,
            'formgrille' => $formgrille->createView(),
     
            'croisiere_excursion'=>$croisiereExcursionRepository->findByExcursion($croisiere),
            //'excursions' => $voyageExcursionRepository->findByVoyageExcursion($voyageOrganiser),
        ]);
       
    }
    

    /**
     * @Route("/{id}/edit", name="app_croisiere_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Croisiere $croisiere, CroisiereRepository $croisiereRepository): Response
    {
        $form = $this->createForm(CroisiereType::class, $croisiere);
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
                $croisiere->addImage($img);
            }
            $croisiereRepository->add($croisiere);
            return $this->redirectToRoute('app_croisiere_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('croisiere_user/edit.html.twig', [
            'croisiere' => $croisiere,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="app_croisiere_user_delete", methods={"POST"})
     */
    public function delete(Request $request, Croisiere $croisiere, CroisiereRepository $croisiereRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$croisiere->getId(), $request->request->get('_token'))) {
            $croisiereRepository->remove($croisiere);
        }

        return $this->redirectToRoute('app_croisiere_user_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("image/delete/{id}" , name="image_delete")
     */
    public function deleteImages($id)
    {
        $em = $this->getDoctrine()->getManager();
        $images = $this->getDoctrine()->getRepository(Images::class);
        $images = $images->find($id);
        $croisiere = $images->getOffres($id);
        if (!$images) {
            throw $this->createNotFoundException(
                'There are no articles with the following id: ' . $id
            );
        }
        $em->remove($images);
        $em->flush();
        return $this->redirectToRoute('app_croisiere_user_edit', ['id' => $croisiere->getId()]);
    }
}
