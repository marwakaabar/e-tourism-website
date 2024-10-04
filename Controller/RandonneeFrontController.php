<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RondonneeRepository;
use App\Repository\GrilleTarifaireRepository;
use App\Repository\PaysRepository;
use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ReservationRepository;
use App\Entity\Reservation;
use App\Repository\AvisRepository;
use App\Repository\OffresRepository;
use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\ClientRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
class RandonneeFrontController extends AbstractController
{
    /**
     * @Route("/randonnee/front", name="app_randonnee_front")
     */
   
    public function randonnee(RondonneeRepository $randonneeRepository,PaysRepository $paysRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $rondonnee = $randonneeRepository->findAll();
        $countrando = count($randonneeRepository->findAll());
        $pay = $paysRepository->findAll();
        $pay = $paginator->paginate(
            $pay, /* query NOT result */
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('Client/Randonnee/index.html.twig', [
            'controller_name' => 'RandonneeFrontController',
            'rondonnee' => $rondonnee,
            'pays' => $pay,
            'countrando' => $countrando,

        ]);
    }
    /**
     * @Route("/detailsrandonnee/{id}", name="app_detail_randonnee", methods={"GET"})
     */
    public function DetailVoyage(RondonneeRepository $randonneeRepository, string $id, PaysRepository $paysRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $rondonnee = $randonneeRepository->findByVoyagePay($id);
        $pay = $paysRepository->findAll();
        $rondonnee = $paginator->paginate(
            $rondonnee, /* query NOT result */
            $request->query->getInt('page', 1),
            4 
        );
        return $this->render('Client/Randonnee/Detail.html.twig', [
            'controller_name' => 'RandonneeFrontController',
            'rondonnee' => $rondonnee,
            'pays' => $pay,


        ]);
    }
    /**
     * @Route("/packageDetailsrandonnee/{id}", name="app_package_Details_randonnee",  methods={"GET", "POST"})
     */
    public function PackageDetails(RondonneeRepository $randonneeRepository,AvisRepository $avisRepository, Request $request, ReservationRepository $reservationRepository, string $id, GrilleTarifaireRepository $grilletarifaireRepository, PaysRepository $paysRepository, ClientRepository $clientRepository,OffresRepository $OffresRepository): Response
    {
        $rondonnee = $randonneeRepository->findById($id);
        $offre = $OffresRepository->findAll();
        $pay = $paysRepository->findAll();
        $grilletarifaires =  $grilletarifaireRepository->findByGrille($id);
        $avi = new Avis();
        $avis=$avisRepository->findByOffre($id);
        $countavis = count($avisRepository->findByOffre($id));
        $formAvis = $this->createForm(AvisType::class, $avi);
        $formAvis->handleRequest($request);
        if ($formAvis->isSubmitted() && $formAvis->isValid()) {
            $rondonneeSelectioner = $randonneeRepository->findOneBy(['id' => $id]);
            $avi->setDate(new \DateTime('now'));
            $avi->setStatut("true");
            $avi->setOffre($rondonneeSelectioner);

            $avisRepository->add($avi, true);
            
            return $this->redirectToRoute('app_package_Details_randonnee' ,['id'=>$id]);
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
             $rondonneeSelectioner = $randonneeRepository->findOneBy(['id' => $id]);
                $clientRepository->add($client);
                $idclient = $clientRepository->find($client->getId());
                $client =  $clientRepository->find($idclient);
                $reservation = new Reservation();
                $reservation->setClient($client);
                $reservation->setGrilleTarifaire($grilletarifaire);       
                 $reservation->setStatus("non_traitee");
                 $reservation->setAgence($rondonneeSelectioner->getAgence());
                $reservation->setOffre($rondonneeSelectioner);
                $reservation->setDateCreation(new \DateTime('now'));
                $reservationRepository->add($reservation);
                return $this->redirectToRoute('app_confirmation_rondonnee' ,['id'=>$id]);
            }

            return  $this->renderForm('Client/Randonnee/reserver.html.twig', [
                'rondonnee' => $rondonnee,
                'adultes' => $adultes,
                'enfants' => $enfants,
                'grilletarifaire' => $grilletarifaire,
                'client' => $client,
                'form' => $form,
              
            ]);
        }

        return $this->renderForm('Client/Randonnee/PackageDetails.html.twig', [
            'rondonnee' => $rondonnee,
            'pays' => $pay,
            'grilletarifaire' => $grilletarifaires,
            'formAvis' => $formAvis,
            'countavis'=>$countavis,
            'avis'=>$avis,
            'offre' => $offre,

        ]);
    }


    
 /**
     * @Route("/confirmation/{id}", name="app_confirmation_rondonnee", methods={"GET"})
     */
    public function Confirmation(RondonneeRepository $randonneeRepository, ClientRepository $clientRepository,string $id, PaysRepository $paysRepository ,GrilleTarifaireRepository $grilletarifaireRepository): Response
    {
        $rondonnee = $randonneeRepository->findByVoyagePay($id);
      
        $grilletarifaires =  $grilletarifaireRepository->findByGrille($id);
        return $this->render('Client/Randonnee/confirmation.html.twig', [
            'controller_name' => 'HomeController',
            'rondonnee' => $rondonnee,
         
            'grilletarifaire' => $grilletarifaires,
          

        ]);
    }
     
}