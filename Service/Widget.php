<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Article;
use App\Entity\Excursion;
use App\Entity\Categorie;
use App\Entity\Pays;
use App\Entity\Offres;
use App\Entity\Sites;

class Widget
{

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em =  $doctrine->getManager();
    }

    public function getBlogs(Pays $pays=null): ?array
    {
        if (!is_null($pays))
            $articles = $this->em->getRepository(Article::class)->findByPays($pays);
        else
            $articles = $this->em->getRepository(Article::class)->findAll();

        return $articles;
    }

    public function getExcursions(Pays $pays=null): ?array
    {
        if (!is_null($pays))
            $excursions = $this->em->getRepository(Excursion::class)->findByPays($pays);
        else
            $excursions = $this->em->getRepository(Excursion::class)->findAll();

        return $excursions;
    }

    public function getSites(Pays $pays=null): ?array
    {
        if (!is_null($pays))
            $site = $this->em->getRepository(Sites::class)->findByPays($pays);
        else
            $site = $this->em->getRepository(Sites::class)->findAll();

        return $site;
    }

    public function getOffres(Pays $pays=null, Categorie $categorie=null): ?array
    {
        if (!is_null($pays))
            $offre = $this->em->getRepository(Offres::class)->findByPays($pays);
        elseif (!is_null($categorie)) 
            $offre = $this->em->getRepository(Offres::class)->findByCategorie($categorie);
        else
            $offre = $this->em->getRepository(Offres::class)->findAll();

        return $offre;
    }
}