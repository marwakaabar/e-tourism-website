<?php

namespace App\Controller;

use App\Entity\Rondonnee;
use App\Entity\Images;
use App\Form\RondonneeType;
use App\Repository\ClientRepository;
use App\Repository\GrilleTarifaireRepository;
use App\Form\GrilleType;
use App\Entity\GrilleTarifaire;
use App\Repository\RondonneeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/rondonnee/admin")
 */
class RondonneeAdminController extends AbstractController
{
    /**
     * @Route("/", name="app_rondonnee_admin_index", methods={"GET"})
     */
    public function index(RondonneeRepository $rondonneeRepository): Response
    {
        return $this->render('rondonnee_admin/index.html.twig', [
            'rondonnees' => $rondonneeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_rondonnee_admin_new", methods={"GET", "POST"})
     */
    public function new(Request $request, RondonneeRepository $rondonneeRepository): Response
    {
        $rondonnee = new Rondonnee();
        $form = $this->createForm(RondonneeType::class, $rondonnee);
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
                $rondonnee->addImage($img);
            }
            $rondonneeRepository->add($rondonnee);
            return $this->redirectToRoute('app_rondonnee_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rondonnee_admin/new.html.twig', [
            'rondonnee' => $rondonnee,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_rondonnee_admin_show", methods={"GET","POST"})
     */
    public function show(Rondonnee $rondonnee, Request $request,ClientRepository $clientRepository,GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {

        $clients= $clientRepository->findAll();
     
        $grilletarifaire = new Grilletarifaire();
        $formgrille = $this->createForm(GrilleType::class, $grilletarifaire);
        $formgrille->handleRequest($request);
        $grilletarifaires= $rondonnee->getGrilletarifaires(); 
        if ($formgrille->isSubmitted() && $formgrille->isValid()) {
            $grilletarifaire->setOffre($rondonnee);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($grilletarifaire);
            $entityManager->flush();
          

            return $this->redirectToRoute('app_rondonnee_admin_show', ['id' => $rondonnee->getId()] );
        }
       
       

        return $this->render('rondonnee_admin/show.html.twig', [
            'grilletarifaires' => $grilletarifaires,
            'rondonnee' => $rondonnee,
            'clients'=>$clients,
            'formgrille' => $formgrille->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_rondonnee_admin_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Rondonnee $rondonnee, RondonneeRepository $rondonneeRepository): Response
    {
        $form = $this->createForm(RondonneeType::class, $rondonnee);
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
                $rondonnee->addImage($img);
            }
            $rondonneeRepository->add($rondonnee);
            return $this->redirectToRoute('app_rondonnee_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rondonnee_admin/edit.html.twig', [
            'rondonnee' => $rondonnee,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/del", name="app_rondonnee_admin_delete", methods={"POST"})
     */
    public function delete(Request $request, Rondonnee $rondonnee, RondonneeRepository $rondonneeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rondonnee->getId(), $request->request->get('_token'))) {
            $rondonneeRepository->remove($rondonnee);
        }

        return $this->redirectToRoute('app_rondonnee_admin_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("image/delete/{id}" , name="image_delete_rondonnee_admin")
     */
    public function deleteImages($id)
    {
        $em = $this->getDoctrine()->getManager();
        $images = $this->getDoctrine()->getRepository(Images::class);
        $images = $images->find($id);
        $rondonnee = $images->getOffres($id);
        if (!$images) {
            throw $this->createNotFoundException(
                'There are no articles with the following id: ' . $id
            );
        }
        $em->remove($images);
        $em->flush();
        return $this->redirectToRoute('app_rondonnee_admin_edit', ['id' => $rondonnee->getId()]);
    }
}
