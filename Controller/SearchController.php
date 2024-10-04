<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\OffresRepository;
class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="app_search", methods={"GET"})
     */
    public function index(SessionInterface $session,OffresRepository $offre): Response
    {
        $data=$session->get('mysearchedata');
        return $this->render('Client/home/search.html.twig',['offre'=>$data]);
       
    }
}
