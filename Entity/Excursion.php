<?php

namespace App\Entity;
use App\Entity\Offres;
use App\Repository\ExcursionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExcursionRepository::class)
 */
class Excursion extends Offres
{
    /**
     * @ORM\OneToMany(targetEntity=VoyageExcursion::class, mappedBy="excursion")
     */
    private $voyageExcursions;

    /**
     * @ORM\OneToMany(targetEntity=CroisiereExcursion::class, mappedBy="excursion")
     */
    private $croisiereExcursions;

    public function __construct()
    {
        parent::__construct();
        $this->voyageExcursions = new ArrayCollection();
        $this->croisiereExcursions = new ArrayCollection();
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
            $voyageExcursion->setExcursion($this);
        }

        return $this;
    }

    public function removeVoyageExcursion(VoyageExcursion $voyageExcursion): self
    {
        if ($this->voyageExcursions->removeElement($voyageExcursion)) {
            // set the owning side to null (unless already changed)
            if ($voyageExcursion->getExcursion() === $this) {
                $voyageExcursion->setExcursion(null);
            }
        }

        return $this;
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
            $croisiereExcursion->setExcursion($this);
        }

        return $this;
    }

    public function removeCroisiereExcursion(CroisiereExcursion $croisiereExcursion): self
    {
        if ($this->croisiereExcursions->removeElement($croisiereExcursion)) {
            // set the owning side to null (unless already changed)
            if ($croisiereExcursion->getExcursion() === $this) {
                $croisiereExcursion->setExcursion(null);
            }
        }

        return $this;
    }
}
