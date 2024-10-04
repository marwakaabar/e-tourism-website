<?php

namespace App\Entity;
use App\Entity\Offres;
use App\Repository\VoyageOrganiserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VoyageOrganiserRepository::class)
 */
class VoyageOrganiser extends Offres
{
    

   
    /**
     * @ORM\OneToMany(targetEntity=VoyageExcursion::class, mappedBy="voyage")
     */
    private $voyageExcursions;

    public function __construct()
    {
        parent::__construct();
        $this->voyageExcursions = new ArrayCollection();
    }

   

    

    /**
     * @return Collection<int, VoyageExcursion>
     */
    public function getVoyageExcursions(): Collection
    {
        return $this->voyageExcursions;
    }

    public function addVoyageExcursion(VoyageExcursion $voyageExcursion): self
    {
        if (!$this->voyageExcursions->contains($voyageExcursion)) {
            $this->voyageExcursions[] = $voyageExcursion;
            $voyageExcursion->setVoyage($this);
        }

        return $this;
    }

    public function removeVoyageExcursion(VoyageExcursion $voyageExcursion): self
    {
        if ($this->voyageExcursions->removeElement($voyageExcursion)) {
            // set the owning side to null (unless already changed)
            if ($voyageExcursion->getVoyage() === $this) {
                $voyageExcursion->setVoyage(null);
            }
        }

        return $this;
    }
}
