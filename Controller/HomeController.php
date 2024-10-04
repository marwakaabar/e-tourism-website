<?php

namespace App\Controller;

use App\Form\SearchOffreType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\OffresRepository;
use App\Repository\VoyageOrganiserRepository;
use App\Repository\PaysRepository;
use App\Repository\ExcursionRepository;
use App\Repository\RondonneeRepository;
use App\Repository\HotelRepository;
use App\Repository\OmraRepository;
use App\Repository\GrilleTarifaireRepository;
use App\Repository\ReservationRepository;
use App\Entity\Reservation;
use App\Entity\Client;
use App\Repository\VoyageExcursionRepository;
use App\Form\ClientType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ClientRepository;
use App\Repository\AgenceRepository;
use App\Repository\ArticleRepository;
use App\Repository\AvisRepository;
use App\Repository\CroisiereRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Avis;
use App\Form\AvisType;
class HomeController extends AbstractController
{
   /**
     * @Route("/", name="app_home", methods={"GET","POST"})
     */
    public function index(VoyageOrganiserRepository $VoyageOrganiserRepository,
    OmraRepository $OmraRepository,
    OffresRepository $OffresRepository, 
    Request $request,  
    PaysRepository $paysRepository,
    AgenceRepository $agenceRepository,
    CroisiereRepository $croisiereRepository,
    RondonneeRepository $randonneeRepository,
    SessionInterface $session): Response
    {
        $offres = $OffresRepository->findAll();
        $countomra = count($OmraRepository->findAll());
        $countcruis = count($croisiereRepository->findAll());
        $countrando = count($randonneeRepository->findAll());
        $countvoyage = $countvoyage = count($VoyageOrganiserRepository->findAll());

        $agence = $agenceRepository->findAll();
        $pay = $paysRepository->findAll();
        $form = $this->createForm(SearchOffreType::class);

        $search = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On recherche les offres correspondant aux mots clés
            $offre = $OffresRepository->search(
                $search->get('mots')->getData(),
                $search->get('categorie')->getData(),
                $search->get('pays')->getData(),
             

            );
            

            return $this->render('Client/home/search.html.twig',['offres'=>$offre]);
        }
        return $this->render('Client/home/index.html.twig', [
            'offres' => $offres,
            'countomra' => $countomra,
            'countcruis'=>$countcruis,
            'countrando'=>$countrando,
            'agences' => $agence,
            'pays' => $pay,
            'countvoyage'=>$countvoyage,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/destination", name="app_destination", methods={"GET"})
     */
    public function Destination(VoyageOrganiserRepository $VoyageOrganiserRepository, PaysRepository $paysRepository ,Request $request,PaginatorInterface $paginator): Response
    {
        $voyageOrganiser = $VoyageOrganiserRepository->findAll();
        $countvoyageOrganiser= count($VoyageOrganiserRepository->findAll());
        $pay = $paysRepository->findAll();
        $pay = $paginator->paginate(
            $pay, /* query NOT result */
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('Client/destination/index.html.twig', [
            'controller_name' => 'HomeController',
            'voyageOrganiser' => $voyageOrganiser,
            'pays' => $pay,
            'countvoyageOrganiser' =>$countvoyageOrganiser,
        ]);
    }

    /**
     * @Route("/detailVoyage/{id}", name="app_detail", methods={"GET"})
     */
    public function DetailVoyage(VoyageOrganiserRepository $VoyageOrganiserRepository, string $id, PaysRepository $paysRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $voyageOrganiser = $VoyageOrganiserRepository->findByVoyagePay($id);
        $pay = $paysRepository->findAll();
        $voyageOrganiser = $paginator->paginate(
            $voyageOrganiser, /* query NOT result */
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('Client/destination/Detail.html.twig', [
            'controller_name' => 'HomeController',
            'voyageOrganiser' => $voyageOrganiser,
            'pays' => $pay,


        ]);
    }
     /**
     * @Route("/packageDetailsOffre/{id}", name="app_package_Details_offre" , methods={"GET", "POST"})
     */
    public function PackageDetailsOffre(OffresRepository $offreRepository,AvisRepository $avisRepository,ReservationRepository $reservationRepository,Request $request ,string $id,GrilleTarifaireRepository $grilletarifaireRepository, PaysRepository $paysRepository, ClientRepository $clientRepository,HotelRepository $hotelRepository): Response
    {
        $hotels =$hotelRepository->findAll();
        $offre = $offreRepository->findById($id);
        $pay = $paysRepository->findAll();
        $grilletarifaires =  $grilletarifaireRepository->findByGrille($id);
        $avi = new Avis();
        $avis=$avisRepository->findByOffre($id);
        $countavis = count($avisRepository->findByOffre($id));
        $formAvis = $this->createForm(AvisType::class, $avi);
        $formAvis->handleRequest($request);
        if ($formAvis->isSubmitted() && $formAvis->isValid()) {
            $offreSelectioner = $offreRepository->findOneBy(['id' => $id]);
            $avi->setDate(new \DateTime('now'));
            $avi->setStatut("true");
            $avi->setOffre($offreSelectioner);

            $avisRepository->add($avi, true);
            
            return $this->redirectToRoute('app_package_Details_offre' ,['id'=>$id]);
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
             $offreSelectioner = $offreRepository->findOneBy(['id' => $id]);
                $clientRepository->add($client);
                $idclient = $clientRepository->find($client->getId());
                $client =  $clientRepository->find($idclient);
                $reservation = new Reservation();
                $reservation->setClient($client);
                $reservation->setGrilleTarifaire($grilletarifaire);       
                 $reservation->setStatus("non_traitee");
                 $reservation->setAgence($offreSelectioner->getAgence());
                $reservation->setOffre($offreSelectioner);
                $reservation->setDateCreation(new \DateTime('now'));
                $reservationRepository->add($reservation);
                return $this->redirectToRoute('app_confirmation_offre' ,['id'=>$id]);
            }

            return  $this->renderForm('Client/home/reserver.html.twig', [
                'offre' => $offre,
                'adultes' => $adultes,
                'enfants' => $enfants,
                'grilletarifaire' => $grilletarifaire,
                'client' => $client,
                'form' => $form,
            
              
            ]);
        }

        return $this->renderForm('Client/home/PackageDetails.html.twig', [
            'offre' => $offre,
            'pays' => $pay,
            'grilletarifaire' => $grilletarifaires,
            'formAvis' => $formAvis,
            'countavis'=>$countavis,
            'avis'=>$avis,
            'hotels' => $hotels,

        ]);
        }

    
    /**
     * @Route("/packageDetailsOmra/{id}", name="app_package_Details_omra" , methods={"GET", "POST"})
     */
    public function PackageDetailsOmra(OmraRepository $omraRepository,AvisRepository $avisRepository,string $id,Request $request, ReservationRepository $reservationRepository, GrilleTarifaireRepository $grilletarifaireRepository, PaysRepository $paysRepository, ClientRepository $clientRepository,OffresRepository $OffresRepository,HotelRepository $hotelRepository): Response
    {
        $omra = $omraRepository->findById($id);
        $offre=$OffresRepository->findAll();
        $hotels =$hotelRepository->findAll();
        $pay = $paysRepository->findAll();
        $grilletarifaires =  $grilletarifaireRepository->findByGrille($id);
        $avi = new Avis();
        $avis=$avisRepository->findByOffre($id);
        $countavis = count($avisRepository->findByOffre($id));
        $formAvis = $this->createForm(AvisType::class, $avi);
        $formAvis->handleRequest($request);
        if ($formAvis->isSubmitted() && $formAvis->isValid()) {
            $omraSelectioner = $omraRepository->findOneBy(['id' => $id]);
            $avi->setDate(new \DateTime('now'));
            $avi->setStatut("true");
            $avi->setOffre($omraSelectioner);

            $avisRepository->add($avi, true);
            
            return $this->redirectToRoute('app_package_Details_omra' ,['id'=>$id]);
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
             $omraSelectioner = $omraRepository->findOneBy(['id' => $id]);
                $clientRepository->add($client);
                $idclient = $clientRepository->find($client->getId());
                $client =  $clientRepository->find($idclient);
                $reservation = new Reservation();
                $reservation->setClient($client);
                $reservation->setGrilleTarifaire($grilletarifaire);       
                 $reservation->setStatus("non_traitee");
                 $reservation->setAgence($omraSelectioner->getAgence());
                $reservation->setOffre($omraSelectioner);
                $reservation->setDateCreation(new \DateTime('now'));
                $reservationRepository->add($reservation);
                return $this->redirectToRoute('app_confirmation_voyageOrganiser' ,['id'=>$id]);
            }

            return  $this->renderForm('Client/Omra/reserver.html.twig', [
                'omra' => $omra,
                'adultes' => $adultes,
                'enfants' => $enfants,
                'grilletarifaire' => $grilletarifaire,
                'client' => $client,
                'form' => $form,
              
            ]);
        }

        return $this->renderForm('Client/Omra/PackageDetails.html.twig', [
            'omra' => $omra,
            'pays' => $pay,
            'grilletarifaire' => $grilletarifaires,
            'formAvis' => $formAvis,
            'countavis'=>$countavis,
            'avis'=>$avis,
            'offre'=>$offre,
            'hotels' => $hotels,
        ]);
        }

/**
     * @Route("/detailAgence", name="app_detail_agence", methods={"GET"})
     */
    public function DetailAgence(AgenceRepository $AgenceRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $agence = $AgenceRepository->findAll();
        $agence = $paginator->paginate(
            $agence, /* query NOT result */
            $request->query->getInt('page', 1),
            4);
        return $this->render('Client/Agence/Detail.html.twig', [
           
            'agence' => $agence,
           


        ]);
    }
   /**
     * @Route("/blog", name="app_article", methods={"GET"})
     */
    public function Article(Request $request, ArticleRepository $articleRepository,PaginatorInterface $paginator): Response
    {

        $articles = $articleRepository->findAll();
        $articles = $paginator->paginate(
            $articles, /* query NOT result */
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('Client/Article/article.html.twig', [
            'articles' => $articles,




        ]);
    }
    /**
     * @Route("/offres", name="app_offree")
     */
    public function moreOffre(OffresRepository $OffresRepository, Request $request,  PaysRepository $paysRepository, AgenceRepository $agenceRepository,PaginatorInterface $paginator): Response
    {
        $offres = $OffresRepository->findAll();
        $agence = $agenceRepository->findAll();
        $pay = $paysRepository->findAll();
        $offres = $paginator->paginate(
            $offres, /* query NOT result */
            $request->query->getInt('page', 1),
            8
        );
        $form = $this->createForm(SearchOffreType::class);

        $search = $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     // On recherche les annonces correspondant aux mots clés
        //     $offres = $OffresRepository->search(
        //         $search->get('mots')->getData(),
        //         $search->get('categorie')->getData(),
        //         $search->get('pays')->getData(),
        //         // $search->get('grilleTarifaires')->getData()

        //     );
        // }
        return $this->render('Client/home/moreOffre.html.twig', [
            'offres' => $offres,
            'agences' => $agence,
            'pays' => $pay,
            'form' => $form->createView()
        ]);
    }
      /**
     * @Route("/reserverOmra/{id}", name="app_reserver_omra", methods={"GET", "POST"})
     */
    public function ReserverOmra(OmraRepository $omraRepository,string $id, Request $request, ClientRepository $clientRepository): Response
    {
        $omra = $omraRepository->findById($id);
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clientRepository->add($client);
        }
        return $this->renderForm('Client/omra/reserver.html.twig',[
      
            'omra' => $omra,
            'client' => $client,
            'form' => $form,

        ]);
    }

    
 /**
     * @Route("/confirmation/{id}", name="app_confirmation_voyageOrganiser", methods={"GET"})
     */
    public function Confirmation(voyageOrganiserRepository $voyageOrganiserRepository, ClientRepository $clientRepository,string $id, PaysRepository $paysRepository ,GrilleTarifaireRepository $grilletarifaireRepository): Response
    {
        $voyageOrganiser = $voyageOrganiserRepository->findByVoyagePay($id);
      
        $grilletarifaires =  $grilletarifaireRepository->findByGrille($id);
        return $this->render('Client/destination/confirmation.html.twig', [
            'controller_name' => 'HomeController',
            'voyageOrganiser' => $voyageOrganiser,
         
            'grilletarifaire' => $grilletarifaires,
          

        ]);
    }
/**
     * @Route("/confirmationoffre/{id}", name="app_confirmation_offre", methods={"GET"})
     */
    public function ConfirmationOffre(OffresRepository $offreRepository, ClientRepository $clientRepository,string $id, PaysRepository $paysRepository ,GrilleTarifaireRepository $grilletarifaireRepository): Response
    {
        $offre = $offreRepository->findByVoyagePay($id);
      
        $grilletarifaires =  $grilletarifaireRepository->findByGrille($id);
        return $this->render('Client/Offre/confirmation.html.twig', [
            'controller_name' => 'HomeController',
            'offre' => $offre,
         
            'grilletarifaire' => $grilletarifaires,
          

        ]);
    }
    /**
     * @Route("/packageDetails/{id}", name="app_package_Details")
     */
    public function PackageDetails(VoyageOrganiserRepository $voyageOrganiserRepository ,AvisRepository $avisRepository, VoyageExcursionRepository $voyageExcursionRepository,Request $request, ReservationRepository $reservationRepository, string $id, GrilleTarifaireRepository $grilletarifaireRepository, PaysRepository $paysRepository, ClientRepository $clientRepository,HotelRepository $hotelRepository): Response    {
        $voyageOrganiser = $voyageOrganiserRepository->findById($id);
        $hotels =$hotelRepository->findAll();
        $pay = $paysRepository->findAll();
        $grilletarifaires =  $grilletarifaireRepository->findByGrille($id);
        $voyageOrganiser_excursion=$voyageExcursionRepository->findAll();
        $avi = new Avis();
        $avis=$avisRepository->findByOffre($id);
        $countavis = count($avisRepository->findByOffre($id));
        $formAvis = $this->createForm(AvisType::class, $avi);
        $formAvis->handleRequest($request);
        if ($formAvis->isSubmitted() && $formAvis->isValid()) {
            $voyageOrganiserSelectioner = $voyageOrganiserRepository->findOneBy(['id' => $id]);
            $avi->setDate(new \DateTime('now'));
            $avi->setStatut("true");
            $avi->setOffre($voyageOrganiserSelectioner);

            $avisRepository->add($avi, true);
            
            return $this->redirectToRoute('app_package_Details' ,['id'=>$id]);
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
             $voyageOrganiserSelectioner = $voyageOrganiserRepository->findOneBy(['id' => $id]);
                $clientRepository->add($client);
                $idclient = $clientRepository->find($client->getId());
                $client =  $clientRepository->find($idclient);
                $reservation = new Reservation();
                $reservation->setClient($client);
                $reservation->setGrilleTarifaire($grilletarifaire);       
                 $reservation->setStatus("non_traitee");
                $reservation->setOffre($voyageOrganiserSelectioner);
                $reservation->setAgence($voyageOrganiserSelectioner->getAgence());
                $reservation->setDateCreation(new \DateTime('now'));
                $reservationRepository->add($reservation);
                return $this->redirectToRoute('app_confirmation_voyageOrganiser' ,['id'=>$id]);
            }

            return  $this->renderForm('Client/destination/reserver.html.twig', [
                'voyageOrganiser' => $voyageOrganiser,
                'adultes' => $adultes,
                'enfants' => $enfants,
                'grilletarifaire' => $grilletarifaire,
                'client' => $client,
                'form' => $form,
              
            ]);
        }

        return $this->renderForm('Client/destination/PackageDetails.html.twig', [
            'voyageOrganiser' => $voyageOrganiser,
            'pays' => $pay,
            'grilletarifaire' => $grilletarifaires,
            'voyageOrganiser_excursions' => $voyageOrganiser_excursion,
            'formAvis' => $formAvis,
            'countavis'=>$countavis,
            'avis'=>$avis,
            'hotels' => $hotels,
        ]);
        }

    

    
    
    /**
     * @Route("/omrafront", name="app_omra_front")
     */
    public function Omra(OmraRepository $omraRepository ,GrilleTarifaireRepository $grilletarifaireRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $omra = $omraRepository->findAll();
        $grilletarifaire =  $grilletarifaireRepository->findAll();
        $omra = $paginator->paginate(
            $omra, /* query NOT result */
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('Client/Omra/index.html.twig', [
            'controller_name' => 'HomeController',
            'omra' => $omra,
            'grilletarifaire' => $grilletarifaire,



        ]);
    }

   /**
     * @Route("/detailOffre/{id}", name="app_detail_offre", methods={"GET"})
     */
    public function DetailOffre(OffresRepository $OffresRepository, string $id, PaysRepository $paysRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $offres = $OffresRepository->findByVoyagePay($id);
        //$voyageOrganiser = $VoyageOrganiserRepository->findAll();
        //$pays = $paysRepository->findAll();
        $pays = $paysRepository->findAll();
        // $offres = $paginator->paginate(
        //     $offres, /* query NOT result */
        //     $request->query->getInt('page', 1),
        //     6
        // );

        return $this->render('Client/home/Detail.html.twig', [

            'offres' => $offres,
            'pays' => $pays,


        ]);
    } 
   
}