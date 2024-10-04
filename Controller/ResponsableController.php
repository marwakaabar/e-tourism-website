<?php

namespace App\Controller;

use App\Entity\Agent;

use App\Form\ResponsableType;
use App\Repository\AgentRepository;
use App\Repository\HotelRepository;
use App\Repository\OffresRepository;
use App\Repository\ReservationRepository;
use App\Repository\RondonneeRepository;
use App\Repository\UserRepository;
use App\Repository\VoyageOrganiserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/responsable")
 */
class ResponsableController extends AbstractController
{
    private $userPasswordEncoder;
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }
    /**
     * @Route("/", name="app_responsable_index", methods={"GET"})
     */
    public function index(AgentRepository $AgentRepository): Response
    {
        $user = $this->getUser();
        $agence = $user->getAgence();
        return $this->render('responsable/index.html.twig', [
            'users' => $AgentRepository->findByAgence($agence),
        ]);
    }
    /**
     * @Route("/dashbord", name="app_responsable_dashbord", methods={"GET"})
     */
    public function indexResponsable(AgentRepository $agentRepository, OffresRepository $offresRepository, HotelRepository $hotelRepository,ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();
        $agence = $user->getAgence();
        $countoffres = count($offresRepository->findByAgence($agence));
        $counthotel=count($hotelRepository->findAll());

        $countagent = count($agentRepository->findByAgence($agence));
        $countreservation = count($reservationRepository->findByAgence($agence));
        return $this->render('responsable/dashbord.html.twig', [
            'users' => $agentRepository->findByAgence($agence),
            'countoffres' => $countoffres,
            'countreservation' => $countreservation,
            'reservations' => $reservationRepository->findByAgence($agence),
            'offres' => $offresRepository->findByAgence($agence),
            'countagent' => $countagent,
            'counthotel' => $counthotel,
        ]);
    }

    /**
     * @Route("/admin", name="app_responsable_admin", methods={"GET"})
     */
    public function indexAdmin(AgentRepository $agentRepository): Response
    {
        return $this->render('responsable/index.html.twig', [
            'users' => $agentRepository->findAll(),

        ]);
    }
    /**
     * @Route("/new", name="app_responsable_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new Agent();
        $form = $this->createForm(ResponsableType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPassword()) {
                $user->setPassword(
                    $this->userPasswordEncoder->encodePassword($user, $user->getPassword())
                );
                $user->eraseCredentials();
            }
            $roles[] = 'ROLE_RESPONSABLE';
            $user->setRoles($roles);
            $userRepository->add($user);
            return $this->redirectToRoute('app_responsable_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('responsable/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_responsable_show", methods={"GET"})
     */
    public function show(Agent $user): Response
    {
        return $this->render('responsable/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_responsable_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Agent $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(ResponsableType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user);
            return $this->redirectToRoute('app_responsable_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('responsable/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_responsable_delete", methods={"POST"})
     */
    public function delete(Request $request, Agent $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user);
        }

        return $this->redirectToRoute('app_responsable_admin', [], Response::HTTP_SEE_OTHER);
    }
}
