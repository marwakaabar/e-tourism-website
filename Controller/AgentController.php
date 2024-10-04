<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Form\AgentType;
use App\Repository\CategorieRepository;
use App\Repository\AgentRepository;
use App\Repository\HotelRepository;
use App\Repository\OffresRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/agent")
 */
class AgentController extends AbstractController
{
    private $userPasswordEncoder;
    public function __construct( UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }
    /**
     * @Route("/", name="app_agent_index", methods={"GET"})
     */
    public function index(AgentRepository $AgentRepository ): Response
    {
        $user=$this->getUser();
        $agence=$user->getAgence();
        return $this->render('agent/index.html.twig', [
            'users' => $AgentRepository->findByAgence($agence),
           

        ]);
    }
/**
     * @Route("/dashbord", name="app_agent_dashbord", methods={"GET"})
     */
    public function indexAgent(AgentRepository $agentRepository,OffresRepository $offresRepository ,CategorieRepository $categorieRepository,HotelRepository $hotelRepository,ReservationRepository $reservationRepository): Response
    {
        $user=$this->getUser();
        $agence=$user->getAgence();
        $countoffres=count($offresRepository->findByAgence($agence));
        $counthotel=count($hotelRepository->findAll());
        $countCategorie=count($categorieRepository->findAll());
        $countreservation=count($reservationRepository->findByAgence($agence));
        return $this->render('agent/dashbord.html.twig' ,[
             'users' => $agentRepository->findByAgence($agence),
             'countoffres'=>$countoffres,
             'counthotel'=>$counthotel,
             'countCategorie'=>$countCategorie,
             'countreservation'=>$countreservation,
             'reservations' => $reservationRepository->findByAgence($agence),
             'offres' => $offresRepository->findByAgence($agence),
             'clients' => $reservationRepository->findByAgence($agence),
    ]);}
    /**
     * @Route("/new", name="app_agent_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository): Response
    {
     
        
        $user = new Agent();
       
        $form = $this->createForm(AgentType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success','Ajout avec Success');
            if ($user->getPassword()) {
                $user->setPassword(
                    $this->userPasswordEncoder->encodePassword($user, $user->getPassword())
                );
                $user->eraseCredentials();
            }
         
            $roles[]='ROLE_AGENT';
            $user->setRoles($roles);
            $agent=$this->getUser();
            $agence=$agent->getAgence();
            $user->setAgence($agence);
            $userRepository->add($user);
            return $this->redirectToRoute('app_agent_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agent/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_agent_show", methods={"GET"})
     */
    public function show(Agent $user): Response
    {
        return $this->render('agent/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_agent_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Agent $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(AgentType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success','Modifier avec Success');
            $userRepository->add($user);
            return $this->redirectToRoute('app_agent_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agent/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_agent_delete", methods={"POST"})
     */
    public function delete(Request $request, Agent $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user);
        }

        return $this->redirectToRoute('app_agent_index', [], Response::HTTP_SEE_OTHER);
    }
}