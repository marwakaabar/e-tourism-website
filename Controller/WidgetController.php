<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GrilleTarifaireRepository;



use App\Service\Widget;

class WidgetController extends AbstractController
{
    /**
     * @Route("/widgetBlog/{pays}", name="app_widget_blogs")
     */
    public function Blogswidget(Widget $widget,$pays=null) : Response
    {
        $articles = $widget->getBlogs($pays);
        return $this->render('Client/widget/blogs.html.twig', [
        'articles'=> $articles,
        
        ]);
    }
    /**
     * @Route("/widgetExcursion/{pays}", name="app_widget_excursions")
     */

    public function Excursions(Widget $widget,$pays=null) : Response
    {

        $excursions = $widget->getExcursions($pays);
        return $this->render('Client/widget/excursions.html.twig', [
        'excursions'=> $excursions,
        ]);
    }
    /**
     * @Route("/widgetSites/{pays}", name="app_widget_site")
     */

    public function Sites(Widget $widget,$pays=null) : Response
    {
        $site = $widget->getSites($pays);
        return $this->render('Client/widget/sites.html.twig', [
            'sites' => $site,
        ]);
    }

    /**
     * @Route("/widgetOffres/{pays}{categorie}{id}", name="app_widget_offre")
     */

    public function Offres(Widget $widget,$pays=null,$categorie=null,GrilleTarifaireRepository $grilletarifaireRepository) : Response
    {
        $grilletarifaires =  $grilletarifaireRepository->findAll();
        $offre = $widget->getOffres($pays,$categorie);
        return $this->render('Client/widget/offres.html.twig', [
            'offres' => $offre,
            'grilletarifaire' => $grilletarifaires,
        ]);
    }
}