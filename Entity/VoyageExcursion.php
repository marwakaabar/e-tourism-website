<?php

namespace App\Entity;

use App\Repository\VoyageExcursionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VoyageExcursionRepository::class)
 */
class VoyageExcursion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="float" ,nullable=true)
     */
    private $prix;


    

    /**
     * @ORM\ManyToOne(targetEntity=VoyageOrganiser::class, inversedBy="voyageExcursions")
     */
    private $voyage;

    /**
     * @ORM\ManyToOne(targetEntity=Excursion::class, inversedBy="voyageExcursions")
     */
    private $excursion;

   

   
  

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }


   

    public function getVoyage(): ?VoyageOrganiser
    {
        return $this->voyage;
    }

    public function setVoyage(?VoyageOrganiser $voyage): self
    {
        $this->voyage = $voyage;

        return $this;
    }

    public function getExcursion(): ?Excursion
    {
        return $this->excursion;
    }

    public function setExcursion(?Excursion $excursion): self
    {
        $this->excursion = $excursion;

        return $this;
    }

    

    
}
