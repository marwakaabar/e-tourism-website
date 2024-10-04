<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ExcursionRepository;
use App\Repository\PaysRepository;
use App\Entity\Client;
use App\Repository\CroisiereRepository;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use App\Repository\GrilleTarifaireRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
class ExcursionFrontController extends AbstractController
{
    /**
     * @Route("/excursionfront", name="app_excursion_front")
     */
   
    public function excursion(ExcursionRepository $excursionRepository,PaysRepository $paysRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $countExcurs = count($excursionRepository->findAll());
        $excursion = $excursionRepository->findAll();
        $pay = $paysRepository->findAll();
        
        $pay = $paginator->paginate(
            $pay, /* query NOT result */
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('Client/excursion/index.html.twig', [
            'controller_name' => 'ExcursionFrontController',
            'excursion' => $excursion,
            'pays' => $pay,
            'countExcurs' => $countExcurs,

        ]);
    }
     /**
     * @Route("/detailsexcursion/{id}", name="app_detail_excursion", methods={"GET"})
     */
    public function DetailVoyage(ExcursionRepository $excursionRepository, string $id, PaysRepository $paysRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $excursion = $excursionRepository->findByVoyagePay($id);
        $pay = $paysRepository->findAll();
        $excursion = $paginator->paginate(
            $excursion, /* query NOT result */
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('Client/excursion/Detail.html.twig', [
            'controller_name' => 'HomeController',
            'excursion' => $excursion,
            'pays' => $pay,


        ]);
    }
    /**
     * @Route("/packageDetailsexcursion/{id}", name="app_package_Details_excursion", methods={"GET", "POST"})
     */
    
    public function PackageDetails(ExcursionRepository $excursionRepository, AvisRepository $avisRepository, ReservationRepository $reservationRepository, Request $request,string $id,GrilleTarifaireRepository $grilletarifaireRepository, PaysRepository $paysRepository, ClientRepository $clientRepository): Response
    {
        $excursion = $excursionRepository->findById($id);
        $pay = $paysRepository->findAll();
        $grilletarifaires =  $grilletarifaireRepository->findByGrille($id);
        $avi = new Avis();
        $avis=$avisRepository->findByOffre($id);
        $countavis = count($avisRepository->findByOffre($id));
        $formAvis = $this->createForm(AvisType::class, $avi);
        $formAvis->handleRequest($request);
        if ($formAvis->isSubmitted() && $formAvis->isValid()) {
            $excursionSelectioner = $excursionRepository->findOneBy(['id' => $id]);
            $avi->setDate(new \DateTime('now'));
            $avi->setStatut("true");
            $avi->setOffre($excursionSelectioner);

            $avisRepository->add($avi, true);
            
            return $this->redirectToRoute('app_package_Details_excursion' ,['id'=>$id]);
        }
        if ($request->isMethod('POST')) {
            $periode = $request->request->get("periode");
            $adultes = $request->request->get("adultes");
            $enfants = $request->request->get("enfants");
          
            $grilletarifaire =  $grilletarifaireRepository->find(intval($periode));
            $client = new Client();
            $form = $this->createForm(ClientType::class, $client);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
             $excursionSelectioner = $excursionRepository->findOneBy(['id' => $id]);
                $clientRepository->add($client);
                $idclient = $clientRepository->find($client->getId());
                $client =  $clientRepository->find($idclient);
                
                $reservation = new Reservation();
                $reservation->setClient($client);
                $reservation->setGrilleTarifaire($grilletarifaire);       
                 $reservation->setStatus("non_traitee");
                 $reservation->setAgence($excursionSelectioner->getAgence());
                $reservation->setOffre($excursionSelectioner);
                $reservation->setDateCreation(new \DateTime('now'));
                $reservationRepository->add($reservation);
                return $this->redirectToRoute('app_confirmation_excursion' ,['id'=>$id]);
            }

            return  $this->renderForm('Client/excursion/reserver.html.twig', [
                
                'adultes' => $adultes,
                'enfants' => $enfants,
                'grilletarifaire' => $grilletarifaire,
                'client' => $client,
                'excursion' => $excursion,
                'form' => $form,
           
              
            ]);
        }

        return $this->renderForm('Client/excursion/PackageDetails.html.twig', [
            'excursion' => $excursion,
            'pays' => $pay,
            'grilletarifaire' => $grilletarifaires,
            'formAvis' => $formAvis,
            'countavis'=>$countavis,
            'avis'=>$avis,
        
           
            
            

        ]);
    }

   /**
     * @Route("/confirmation/{id}", name="app_confirmation_excursion", methods={"GET"})
     */
    public function Confirmation(ExcursionRepository $excursionRepository, ClientRepository $clientRepository,string $id, PaysRepository $paysRepository ,GrilleTarifaireRepository $grilletarifaireRepository): Response
    {
        $excursion = $excursionRepository->findByVoyagePay($id);
      
        $grilletarifaires =  $grilletarifaireRepository->findByGrille($id);
        return $this->render('Client/excursion/confirmation.html.twig', [
            'controller_name' => 'HomeController',
            'excursion' => $excursion,
         
            'grilletarifaire' => $grilletarifaires,
          

        ]);
    }
}
