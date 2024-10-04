<?php

namespace App\Entity;
use App\Entity\Offres;
use App\Repository\OmraRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OmraRepository::class)
 */
class Omra extends Offres
{
  
    /**
     * @ORM\Column(type="text")
     */
    private $programme;

   
    public function getProgramme(): ?string
    {
        return $this->programme;
    }

    public function setProgramme(string $programme): self
    {
        $this->programme = $programme;

        return $this;
    }
}
