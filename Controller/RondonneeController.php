<?php

namespace App\Controller;
use App\Entity\Images;
use App\Entity\Rondonnee;
use App\Form\RondonneeAgentType;
use App\Form\GrilleType;
use App\Entity\GrilleTarifaire;
use App\Entity\Reservation;
use App\Repository\OffresRepository;
use App\Repository\AgentRepository;
use App\Repository\RondonneeRepository;
use App\Repository\ClientRepository;
use App\Repository\GrilleTarifaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/rondonnee")
 */
class RondonneeController extends AbstractController
{
    /**
     * @Route("/", name="app_rondonnee_index", methods={"GET"})
     */
    public function index(RondonneeRepository $rondonneeRepository, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        $user=$this->getUser();
        $agence=$user->getAgence();
        return $this->render('rondonnee/index.html.twig', [
            'rondonnees' => $rondonneeRepository->findByAgence($agence),
          
        ]);
    }

    /**
     * @Route("/new", name="app_rondonnee_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AgentRepository $agentRepository ,RondonneeRepository $rondonneeRepository): Response
    {
        $rondonnee = new Rondonnee();
        $form = $this->createForm(RondonneeAgentType::class, $rondonnee);
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
            $agent=$this->getUser();
            $agence=$agent->getAgence();
            $rondonnee->setAgence($agence);
            $rondonneeRepository->add($rondonnee);
            return $this->redirectToRoute('app_rondonnee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rondonnee/new.html.twig', [
            'rondonnee' => $rondonnee,
            'form' => $form,
        ]);
    }

     /**
     * @Route("/reservation/offre", name="randonnee_reservation", methods={"POST"})
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
     * @Route("/{id}", name="app_rondonnee_show", methods={"GET","POST"})
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
          

            return $this->redirectToRoute('app_rondonnee_show', ['id' => $rondonnee->getId()] );
        }
       
       

        return $this->render('rondonnee/show.html.twig', [
            'grilletarifaires' => $grilletarifaires,
            'rondonnee' => $rondonnee,
            'clients'=>$clients,
            'formgrille' => $formgrille->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_rondonnee_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Rondonnee $rondonnee, RondonneeRepository $rondonneeRepository): Response
    {
        $form = $this->createForm(RondonneeAgentType::class, $rondonnee);
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
            return $this->redirectToRoute('app_rondonnee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rondonnee/edit.html.twig', [
            'rondonnee' => $rondonnee,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/{id}/del", name="app_rondonnee_delete", methods={"POST"})
     */
    public function delete(Request $request, Rondonnee $rondonnee, RondonneeRepository $rondonneeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rondonnee->getId(), $request->request->get('_token'))) {
            $rondonneeRepository->remove($rondonnee);
        }

        return $this->redirectToRoute('app_rondonnee_index', [], Response::HTTP_SEE_OTHER);
    }
     /**
     * @Route("image/delete/{id}" , name="image_delete_rondonnee")
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
        return $this->redirectToRoute('app_rondonnee_edit', ['id' => $rondonnee->getId()]);
    }
}