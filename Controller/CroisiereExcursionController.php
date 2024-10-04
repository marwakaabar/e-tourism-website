<?php

namespace App\Controller;


use App\Entity\CroisiereExcursion;
use App\Form\CroisiereExcursionType;
use App\Repository\CroisiereExcursionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/croisiere_excursion")
 */
class CroisiereExcursionController extends AbstractController
{
    /**
     * @Route("/", name="app_croisiere_excursion_index", methods={"GET"})
     */
    public function index(CroisiereExcursionRepository $croisiereExcursionRepository): Response
    {
        return $this->render('croisiere_excursion/index.html.twig', [
            'croisiere_excursions' => $croisiereExcursionRepository->findAll(),
            
        ]);
    }

   

    /**
     * @Route("/excursion/{id}", name="app_croisiere_excursion_show", methods={"GET"})
     */
    public function show(CroisiereExcursion $croisiereExcursion): Response
    {
        return $this->render('croisiere_excursion/show.html.twig', [
            'croisiere_excursion' => $croisiereExcursion,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_croisiere_excursion_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, CroisiereExcursion $croisiereExcursion, CroisiereExcursionRepository $croisiereExcursionRepository): Response
    {
        $form = $this->createForm(CroisiereExcursionType::class, $croisiereExcursion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success','Modifier avec Success');
        }
        return $this->renderForm('croisiere_excursion/edit.html.twig', [
            'croisiere_excursion' => $croisiereExcursion,
            'form' => $form,
        ]);
    }
 /**
     * @Route("/newExcursion", name="app_croisiere_excursion_new", methods={"GET", "POST"})
     */
    public function newExcursion(Request $request,CroisiereExcursionRepository $croisiereExcursionRepository): Response
    {
        $croisiereExcursion = new CroisiereExcursion();
        $form = $this->createForm(CroisiereExcursionType::class, $croisiereExcursion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $croisiereExcursionRepository->add($croisiereExcursion);
            return $this->redirectToRoute('app_croisiere_excursion_index',[], Response::HTTP_SEE_OTHER);

        } 
        return $this->renderForm('croisiere_excursion/new.html.twig', [
            'croisiere_excursion' => $croisiereExcursion,
            'form' => $form,
        ]);
    }
    /**
     * @Route("/delete/{id}", name="app_croisiere_excursion_delete", methods={"POST"})
     */
    public function delete(Request $request, CroisiereExcursion $croisiereExcursion, CroisiereExcursionRepository $croisiereExcursionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$croisiereExcursion->getId(), $request->request->get('_token'))) {
            $croisiereExcursionRepository->remove($croisiereExcursion);
        }

        return $this->redirectToRoute('app_croisiere_excursion_index', [], Response::HTTP_SEE_OTHER);
    }

     /**
     * @Route("image/delete/{id}" , name="image_delete_agent")
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
        return $this->redirectToRoute('app_croisiere_edit', ['id' => $croisiere->getId()]);
    }


}
