<?php

namespace App\Entity;
use App\Entity\Offres;
use App\Repository\CroisiereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CroisiereRepository::class)
 */
class Croisiere extends Offres
{
    
    
    /**
     * @ORM\OneToMany(targetEntity=CroisiereExcursion::class, mappedBy="croisiere")
     */
    private $croisiereExcursions;

    public function __construct()
    {
        parent::__construct();
        $this->croisiereExcursions = new ArrayCollection();
    }

   

    

    /**
     * @return Collection<int, CroisiereExcursion>
     */
    public function getCroisiereExcursions(): Collection
    {
        return $this->croisiereExcursions;
    }

    public function addCroisiereExcursion(CroisiereExcursion $croisiereExcursion): self
    {
        if (!$this->croisiereExcursions->contains($croisiereExcursion)) {
            $this->croisiereExcursions[] = $croisiereExcursion;
            $croisiereExcursion->setCroisiere($this);
        }

        return $this;
    }

    public function removeCroisiereExcursion(CroisiereExcursion $croisiereExcursion): self
    {
        if ($this->croisiereExcursions->removeElement($croisiereExcursion)) {
            // set the owning side to null (unless already changed)
            if ($croisiereExcursion->getCroisiere() === $this) {
                $croisiereExcursion->setCroisiere(null);
            }
        }

        return $this;
    }
}
