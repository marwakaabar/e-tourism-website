<?php

namespace App\Controller;
use App\Entity\Images;
use App\Entity\Agence;
use App\Entity\User;
use App\Form\AgenceType;
use App\Repository\AgenceRepository;
use App\Repository\UserRepository;
use App\Repository\AgentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @Route("/agence")
 */
class AgenceController extends AbstractController
{
    /**
     * @Route("/", name="app_agence_index", methods={"GET"})
     */
    public function index(AgenceRepository $agenceRepository, UserRepository $userRepository): Response
    {
        return $this->render('agence/index.html.twig', [
            'agences' => $agenceRepository->findAll(),
        ]);
       
    }

    /**
     * @Route("/new", name="app_agence_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AgenceRepository $agenceRepository): Response
    {
        $agence = new Agence();
        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success','Ajouter avec Success');
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
                $agence->addImage($img);
            }
            $agenceRepository->add($agence);
            return $this->redirectToRoute('app_agence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agence/new.html.twig', [
            'agence' => $agence,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_agence_show", methods={"GET"})
     */
    public function show(Agence $agence , $id ,AgentRepository $agentRepository): Response
    {
       // $users = $this->getDoctrine()->getRepository(Agent::class)->findAll($id);
        return $this->render('agence/show.html.twig', [
            'agence' => $agence,
            'users' => $agentRepository->findByAgence($agence),
        ]);
        
    }

    /**
     * @Route("/{id}/edit", name="app_agence_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Agence $agence, AgenceRepository $agenceRepository): Response
    {
        $form = $this->createForm(AgenceType::class, $agence);
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
                $agence->addImage($img);
            }
            $agenceRepository->add($agence);
            return $this->redirectToRoute('app_agence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agence/edit.html.twig', [
            'agence' => $agence,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_agence_delete", methods={"POST"})
     */
    public function delete(Request $request, Agence $agence, AgenceRepository $agenceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$agence->getId(), $request->request->get('_token'))) {
            $agenceRepository->remove($agence);
        }

        return $this->redirectToRoute('app_agence_index', [], Response::HTTP_SEE_OTHER);
    }
  /**
     * @Route("image/delete/{id}" , name="image_delete_agence")
     */
    public function deleteImages($id)
    {
        $em = $this->getDoctrine()->getManager();
        $images = $this->getDoctrine()->getRepository(Images::class);
        $images = $images->find($id);
        $agence = $images->getAgence($id);
        if (!$images) {
            throw $this->createNotFoundException(
                'There are no articles with the following id: ' . $id
            );
        }
        $em->remove($images);
        $em->flush();
        return $this->redirectToRoute('app_agence_edit', ['id' => $agence->getId()]);
    }
 
}
