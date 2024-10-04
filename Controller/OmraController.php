<?php

namespace App\Controller;

use App\Entity\Omra;
use App\Entity\Images;
use App\Entity\GrilleTarifaire;
use App\Form\GrilleTarifaireType;
use App\Form\OmraAgentType;
use App\Repository\ClientRepository;
use App\Repository\OmraRepository;
use App\Repository\GrilleTarifaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Reservation;
use App\Repository\OffresRepository;
/**
 * @Route("/omra")
 */
class OmraController extends AbstractController
{
    /**
     * @Route("/", name="app_omra_index", methods={"GET"})
     */
    public function index(OmraRepository $omraRepository, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        $user=$this->getUser();
        $agence=$user->getAgence();
        return $this->render('omra/index.html.twig', [
            'omras' => $omraRepository->findByAgence($agence),
            //'grille_tarifaires' => $grilleTarifaireRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_omra_new", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function new(Request $request, OmraRepository $omraRepository): Response
    {
           $omra = new Omra();
            $form = $this->createForm(OmraAgentType::class, $omra);
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
                    $omra->addImage($img);
                }
                $agent=$this->getUser();
                $agence=$agent->getAgence();
                $omra->setAgence($agence);
                $omraRepository->add($omra);
                return $this->redirectToRoute('app_omra_index', [], Response::HTTP_SEE_OTHER);
            }
    
            return $this->renderForm('omra/new.html.twig', [
                'omra' => $omra,
                'form' => $form,
            ]);
        }
    
    
/**
     * @Route("/reservation/offre", name="omra_reservation", methods={"POST"})
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
     * @Route("/{id}", name="app_omra_show", methods={"GET", "POST"})
     */
    public function show(Omra $omra,ClientRepository $clientRepository, Request $request,GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        $clients= $clientRepository->findAll();
        $grilletarifaire = new Grilletarifaire();
        $formgrille = $this->createForm(GrilleTarifaireType::class, $grilletarifaire);
        $formgrille->handleRequest($request);
        $grilletarifaires= $omra->getGrilletarifaires(); 
        if ($formgrille->isSubmitted() && $formgrille->isValid()) {
            $grilletarifaire->setOffre($omra);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($grilletarifaire);
            $entityManager->flush();
          

            return $this->redirectToRoute('app_omra_show', ['id' => $omra->getId()] );
        }
       
       

        return $this->render('omra/show.html.twig', [
            'grilletarifaires' => $grilletarifaires,
            'omra' => $omra,
            'clients'=>$clients,

          
            'formgrille' => $formgrille->createView(),
     
           
        ]);
    }
   
    /**
     * @Route("/{id}/edit", name="app_omra_edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Omra $omra, OmraRepository $omraRepository): Response
    {
        $form = $this->createForm(OmraAgentType::class, $omra);
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
                $omra->addImage($img);
            }

            $omraRepository->add($omra);
            return $this->redirectToRoute('app_omra_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('omra/edit.html.twig', [
            'omra' => $omra,
            'form' => $form,
        ]);
    }

    

    /**
     * @Route("/delete/{id}", name="app_omra_delete", methods={"POST"})
     */
    public function delete(Request $request, Omra $omra, OmraRepository $omraRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$omra->getId(), $request->request->get('_token'))) {
            $omraRepository->remove($omra);
        }

        return $this->redirectToRoute('app_omra_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("image/delete/{id}" , name="image_delete_omra")
     */
    public function deleteImages($id)
    {
        $em = $this->getDoctrine()->getManager();
        $images = $this->getDoctrine()->getRepository(Images::class);
        $images = $images->find($id);
        $omra = $images->getOffres($id);
        if (!$images) {
            throw $this->createNotFoundException(
                'There are no articles with the following id: ' . $id
            );
        }
        $em->remove($images);
        $em->flush();
        return $this->redirectToRoute('app_omra_edit', ['id' => $omra->getId()]);
    }
}