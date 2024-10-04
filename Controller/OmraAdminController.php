<?php

namespace App\Controller;

use App\Entity\Omra;
use App\Entity\Images;
use App\Entity\GrilleTarifaire;
use App\Form\GrilleTarifaireType;
use App\Repository\ClientRepository;
use App\Repository\OffresRepository;
use App\Form\OmraType;
use App\Entity\Reservation;
use App\Repository\OmraRepository;
use App\Repository\GrilleTarifaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/omra/admin")
 */
class OmraAdminController extends AbstractController
{
    /**
     * @Route("/", name="app_omra_admin_index", methods={"GET"})
     */
    public function index(OmraRepository $omraRepository): Response
    {
        return $this->render('omra_admin/index.html.twig', [
            'omras' => $omraRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_omra_admin_new", methods={"GET", "POST"})
     */
    public function new(Request $request, OmraRepository $omraRepository): Response
    {
        $omra = new Omra();
        $form = $this->createForm(OmraType::class, $omra);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success','Ajout avec Success');
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
            return $this->redirectToRoute('app_omra_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('omra_admin/new.html.twig', [
            'omra' => $omra,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_omra_admin_show",methods={"GET", "POST"})
     */
    public function show(Omra $omra ,Request $request,GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        $grilletarifaire = new Grilletarifaire();
        $formgrille = $this->createForm(GrilleTarifaireType::class, $grilletarifaire);
        $formgrille->handleRequest($request);
        $grilletarifaires= $omra->getGrilletarifaires(); 
        if ($formgrille->isSubmitted() && $formgrille->isValid()) {
            $grilletarifaire->setOffre($omra);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($grilletarifaire);
            $entityManager->flush();
          

            return $this->redirectToRoute('app_omra_admin_show', ['id' => $omra->getId()] );
        }
       
       

        return $this->render('omra_admin/show.html.twig', [
            'grilletarifaires' => $grilletarifaires,
            'omra' => $omra,
          
            'formgrille' => $formgrille->createView(),
        ]);
    }
 /**
     * @Route("/reservation/offre", name="omra_admin_reservation", methods={"POST"})
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
     * @Route("/{id}/edit", name="app_omra_admin_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Omra $omra, OmraRepository $omraRepository): Response
    {
        $form = $this->createForm(OmraType::class, $omra);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success','Modifier avec Success');
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
            return $this->redirectToRoute('app_omra_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('omra_admin/edit.html.twig', [
            'omra' => $omra,
            'form' => $form,
        ]);
    }
  /**
     * @Route("/delete/{id}", name="app_omra_admin_delete", methods={"POST"})
     */
    public function delete(Request $request, Omra $omra, OmraRepository $omraRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$omra->getId(), $request->request->get('_token'))) {
            $omraRepository->remove($omra);
        }

        return $this->redirectToRoute('app_omra_admin_index', [], Response::HTTP_SEE_OTHER);
    }

    
     /**
     * @Route("image/delete/{id}" , name="image_delete_omra_admin")
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
        return $this->redirectToRoute('app_omra_admin_edit', ['id' => $omra->getId()]);
    }
}
