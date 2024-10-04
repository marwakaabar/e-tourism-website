<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CroisiereRepository;
use App\Repository\PaysRepository;
use App\Repository\ReservationRepository;
use App\Repository\OffresRepository;
use App\Entity\Reservation;
use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use App\Repository\CroisiereExcursionRepository;
use App\Repository\GrilleTarifaireRepository;
use App\Repository\ExcursionRepository;
use App\Repository\AgenceRepository;
use App\Entity\Client;
use App\Entity\GrilleTarifaire;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CroisiereFrontController extends AbstractController
{
    /**
     * @Route("/cruise", name="croisiere")
     */
    public function Croisiere(CroisiereRepository $croisiereRepository, PaysRepository $paysRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $croisiere = $croisiereRepository->findAll();
        $countcroisiere = count($croisiereRepository->findAll());
        $pay = $paysRepository->findAll();
        $pay = $paginator->paginate(
            $pay, /* query NOT result */
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('Client/croisiere/index.html.twig', [
            'controller_name' => 'CroisiereFrontController',
            'croisiere' => $croisiere,
            'pays' => $pay,
            'countcroisiere' => $countcroisiere

        ]);
    }

    /**
     * @Route("/detailsCroisiere/{id}", name="app_detail_croisiere", methods={"GET"})
     */
    public function DetailVoyage(CroisiereRepository $croisiereRepository, string $id,PaysRepository $paysRepository,AgenceRepository $agenceRepository , GrilleTarifaireRepository $grilletarifaireRepository,CroisiereExcursionRepository $CroisiereExcursionRepository,Request $request,PaginatorInterface $paginator ): Response
    {
        $croisiere = $croisiereRepository->findByVoyagePay($id);
        
        $CroisiereExcursion = $CroisiereExcursionRepository->findAll();
        $pay = $paysRepository->findAll();
        $croisiere = $paginator->paginate(
            $croisiere, /* query NOT result */
            $request->query->getInt('page', 1),
            4
        );
        $agence = $agenceRepository->findAll();
        $grilletarifaires =  $grilletarifaireRepository->findByGrille($id);
        return $this->render('Client/croisiere/Detail.html.twig', [
            'controller_name' => 'HomeController',
            'croisiere' => $croisiere,
            'pays' => $pay,
            'CroisiereExcursion' => $CroisiereExcursion,
            'agence' => $agence,
            'grilletarifaire' => $grilletarifaires,


        ]);
    }
    /**
     * @Route("/packageDetailsCroisiere/{id}", name="app_package_Details_croisiere", methods={"GET", "POST"} )
     */
    public function PackageDetails(CroisiereRepository $croisiereRepository, AvisRepository $avisRepository,CroisiereExcursionRepository $croisiereExcursionRepository, Request $request, ReservationRepository $reservationRepository, SessionInterface $session, string $id, GrilleTarifaireRepository $grilletarifaireRepository, PaysRepository $paysRepository, ClientRepository $clientRepository,ExcursionRepository $excursionRepository,AgenceRepository $agenceRepository,OffresRepository $OffresRepository): Response
    {   $pay = $paysRepository->findAll();
        $croisiere = $croisiereRepository->findById($id);

        $croisiereExcursion= $croisiereExcursionRepository->findAll(); 
        $offre=$OffresRepository->findAll();
        $agence=$agenceRepository->findAll();
       
        $grilletarifaires =  $grilletarifaireRepository->findByGrille($id);
        $avi = new Avis();
        $avis=$avisRepository->findByOffre($id);
        $countavis = count($avisRepository->findByOffre($id));
        $formAvis = $this->createForm(AvisType::class, $avi);
        $formAvis->handleRequest($request);
        if ($formAvis->isSubmitted() && $formAvis->isValid()) {
            $croisiereSelectioner = $croisiereRepository->findOneBy(['id' => $id]);
            $avi->setDate(new \DateTime('now'));
            $avi->setStatut("true");
            $avi->setOffre($croisiereSelectioner);

            $avisRepository->add($avi, true);
            
            return $this->redirectToRoute('app_package_Details_croisiere' ,['id'=>$id]);
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
             $croisiereSelectioner = $croisiereRepository->findOneBy(['id' => $id]);
                $clientRepository->add($client);
                $idclient = $clientRepository->find($client->getId());
                $client =  $clientRepository->find($idclient);
                $avisRepository->add($avi, true);
                $avi->setDate(new \DateTime('now'));
                $avi->setStatut("true");
                $reservation = new Reservation();
                $reservation->setClient($client);
                $reservation->setGrilleTarifaire($grilletarifaire);       
                 $reservation->setStatus("non_traitee");
                 $reservation->setAgence($croisiereSelectioner->getAgence());
                $reservation->setOffre($croisiereSelectioner);
                $reservation->setDateCreation(new \DateTime('now'));
                $reservationRepository->add($reservation);
                return $this->redirectToRoute('app_confirmation_croisiere' ,['id'=>$id]);
            }

            return  $this->renderForm('Client/croisiere/reserver.html.twig', [
                'croisiere' => $croisiere,
                'adultes' => $adultes,
                'enfants' => $enfants,
                'grilletarifaire' => $grilletarifaire,
                'client' => $client,
                'form' => $form,
                'agence' => $agence,
                
              
            ]);
        }

        return $this->renderForm('Client/croisiere/PackageDetails.html.twig', [
            'croisiere' => $croisiere,
            'pays' => $pay,
            'grilletarifaire' => $grilletarifaires,
            'croisiere_excursions'=>$croisiereExcursion,
            'agence' => $agence,
            'formAvis' => $formAvis,
            'countavis'=>$countavis,
            'avis'=>$avis,
            'offre'=>$offre,
        
            

        ]);
    }

 
 /**
     * @Route("/confirmation/{id}", name="app_confirmation_croisiere", methods={"GET"})
     */
    public function Confirmation(CroisiereRepository $croisiereRepository, ClientRepository $clientRepository,string $id, PaysRepository $paysRepository ,GrilleTarifaireRepository $grilletarifaireRepository): Response
    {
        $croisiere = $croisiereRepository->findByVoyagePay($id);
      
        $grilletarifaires =  $grilletarifaireRepository->findByGrille($id);
        return $this->render('Client/croisiere/confirmation.html.twig', [
            'controller_name' => 'HomeController',
            'croisiere' => $croisiere,
         
            'grilletarifaire' => $grilletarifaires,
          

        ]);
    }

    
}
