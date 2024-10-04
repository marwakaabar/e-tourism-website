<?php

namespace App\Entity;
use App\Entity\Offres;
use App\Repository\RondonneeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RondonneeRepository::class)
 */
class Rondonnee extends Offres
{
  
}
