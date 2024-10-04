<?php

namespace App\Controller;
use App\Entity\Images;
use App\Entity\VoyageOrganiser;
use App\Form\VoyageOrganiserType;
use App\Entity\GrilleTarifaire;

use App\Repository\VoyageOrganiserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GrilleTarifaireRepository;
use App\Repository\VoyageExcursionRepository;
use App\Form\GrilleTarifaireType;
use App\Repository\ClientRepository;

/**
 * @Route("/voyage/admin")
 */
class VoyageAdminController extends AbstractController
{
    /**
     * @Route("/", name="app_voyage_admin_index", methods={"GET"})
     */
    public function index(VoyageOrganiserRepository $voyageOrganiserRepository): Response
    {
        return $this->render('voyage_admin/index.html.twig', [
            'voyage_organisers' => $voyageOrganiserRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_voyage_admin_new", methods={"GET", "POST"})
     */
    public function new(Request $request, VoyageOrganiserRepository $voyageOrganiserRepository): Response
    {
        $voyageOrganiser = new VoyageOrganiser();
        $form = $this->createForm(VoyageOrganiserType::class, $voyageOrganiser);
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
            $voyageOrganiserRepository->add($voyageOrganiser);
            return $this->redirectToRoute('app_voyage_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voyage_admin/new.html.twig', [
            'voyage_organiser' => $voyageOrganiser,
            'form' => $form,
        ]);
    }
/**
     * @Route("/{id}", name="app_voyage_admin_show", methods={"GET", "POST"})
     */
    public function show(VoyageOrganiser $voyageOrganiser ,ClientRepository $clientRepository, Request $request,GrilleTarifaireRepository $grilleTarifaireRepository,VoyageExcursionRepository $voyageExcursionRepository): Response
    {
        $clients= $clientRepository->findAll();
        $voyage_excursion=$voyageExcursionRepository->findAll();
        $grilletarifaire = new Grilletarifaire();
        $formgrille = $this->createForm(GrilleTarifaireType::class, $grilletarifaire);
        $formgrille->handleRequest($request);
        $grilletarifaires= $voyageOrganiser->getGrilletarifaires(); 
        if ($formgrille->isSubmitted() && $formgrille->isValid()) {
            $grilletarifaire->setOffre($voyageOrganiser);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($grilletarifaire);
            $entityManager->flush();
          

            return $this->redirectToRoute('app_voyage_admin_show', ['id' => $voyageOrganiser->getId()] );
        }
       
       

        return $this->render('voyage_admin/show.html.twig', [
            'grilletarifaires' => $grilletarifaires,
            'voyage_organiser' => $voyageOrganiser,
            'clients'=>$clients,
            "voyage_excursions"=>$voyage_excursion,
          
            'formgrille' => $formgrille->createView(),
     
            'voyage_excursion'=>$voyageExcursionRepository->findByExcursion($voyageOrganiser),
            //'excursions' => $voyageExcursionRepository->findByVoyageExcursion($voyageOrganiser),
        ]);
    }

    

    /**
     * @Route("/{id}/edit", name="app_voyage_admin_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, VoyageOrganiser $voyageOrganiser, VoyageOrganiserRepository $voyageOrganiserRepository): Response
    {
        $form = $this->createForm(VoyageOrganiserType::class, $voyageOrganiser);
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
            return $this->redirectToRoute('app_voyage_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voyage_admin/edit.html.twig', [
            'voyage_organiser' => $voyageOrganiser,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_voyage_admin_delete", methods={"POST"})
     */
    public function delete(Request $request, VoyageOrganiser $voyageOrganiser, VoyageOrganiserRepository $voyageOrganiserRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$voyageOrganiser->getId(), $request->request->get('_token'))) {
            $voyageOrganiserRepository->remove($voyageOrganiser);
        }

        return $this->redirectToRoute('app_voyage_admin_index', [], Response::HTTP_SEE_OTHER);
    }
     /**
     * @Route("image/delete/{id}" , name="image_delete_voyage_admin")
     */
    public function deleteImages($id)
    {
        $em = $this->getDoctrine()->getManager();
        $images = $this->getDoctrine()->getRepository(Images::class);
        $images = $images->find($id);
        $voyageOrganiser = $images->getOffres($id);
        if (!$images) {
            throw $this->createNotFoundException(
                'There are no articles with the following id: ' . $id
            );
        }
        $em->remove($images);
        $em->flush();
        return $this->redirectToRoute('app_voyage_admin_edit', ['id' => $voyageOrganiser->getId()]);
    }
}
