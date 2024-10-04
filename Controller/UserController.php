<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UpdateType;
use App\Repository\AgenceRepository;
use App\Repository\UserRepository;
use App\Repository\AgentRepository;
use App\Repository\ClientRepository;
use App\Repository\OffresRepository;
use App\Repository\PaysRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private $userPasswordEncoder;
    public function __construct( UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }
    /**
     * @Route("/", name="app_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository ): Response
    {
        $user=$this->getUser();
        $role=$user->getRoles();
        if (in_array("ROLE_AGENT", $role)) 
            return $this->redirectToRoute('app_agent_dashbord');
            if (in_array("ROLE_RESPONSABLE", $role)) 
              return $this->redirectToRoute('app_responsable_dashbord');
    
        
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
      
        ]);
    }
/**
     * @Route("/dashbord", name="app_admin_dashbord", methods={"GET"})
     */
    public function indexUser(UserRepository $userRepository ,PaysRepository $paysRepository,AgenceRepository $agenceRepository,OffresRepository $offresRepository,ClientRepository $clientRepository,ReservationRepository $reservationRepository): Response
    {$countpays=count($paysRepository->findAll());
        $countagence=count($agenceRepository->findAll());
        $countoffres=count($offresRepository->findAll());
        $countclient=count($clientRepository->findAll());
        return $this->render('user/dashbord.html.twig' ,[
             'users' => $userRepository->findAll(),
             'countpays'=>$countpays,
             'countagence'=>$countagence,
             'countoffres'=>$countoffres,
             'countclient'=>$countclient,
             'offres' => $offresRepository->findBy([],['id' => 'desc']),
             'reservations' => $reservationRepository->findBy([],['id' => 'desc']),
    ]);
            
    }
 /**
     * @Route("/agent", name="agent_index", methods={"GET"})
     */
    public function indexAgent(AgentRepository $AgentRepository): Response
    {
        
        return $this->render('agent/index.html.twig', [
            'users' => $AgentRepository->findAll(),
        ]);
    }
    /**
     * @Route("/responsable", name="responsable_index", methods={"GET"})
     */
    public function indexResponsable(UserRepository $UserRepository): Response
    {
       
        return $this->render('responsable/index.html.twig', [
            'users' => $UserRepository->findAll(),
        ]);
    }
    /**
     * @Route("/new", name="app_admin_new", methods={"GET", "POST"})
     */
    public function newAdmin(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPassword()) {
                $user->setPassword(
                    $this->userPasswordEncoder->encodePassword($user, $user->getPassword())
                );
                $user->eraseCredentials();
            }
            $roles[]='ROLE_ADMIN';
            $user->setRoles($roles);
            $userRepository->add($user);
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
 

    /**
     * @Route("/{id}", name="app_admin_show", methods={"GET"})
     */
    public function showAdmin(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
    /**
     * @Route("/{id}/editAdmin", name="admin_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user);
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_admin_edit", methods={"GET", "POST"})
     */
    public function editAdmin(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user);
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_admin_delete", methods={"POST"})
     */
    public function deleteAdmin(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
    * @Route("/user/statistiques", name="statistiques")
    */
  public function statistiques(): Response
  {
    return $this->render('user/dashbord.html.twig');
   
  }

}