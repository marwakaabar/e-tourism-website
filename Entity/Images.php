<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImagesRepository::class)
 */
class Images
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="images")
     * @ORM\JoinColumn(nullable=true)
     * @ORM\JoinColumn(nullable=true)
     */
    private $agence;

    /**
     * @ORM\ManyToOne(targetEntity=Hotel::class, inversedBy="images")
     */
    private $hotel;

    /**
     * @ORM\ManyToOne(targetEntity=Pays::class, inversedBy="images")
     * @ORM\JoinColumn(nullable=true)
     */
    private $pays;

    /**
     * @ORM\ManyToOne(targetEntity=Sites::class, inversedBy="images")
     * @ORM\JoinColumn(nullable=true)
     */
    private $sites;

    /**
     * @ORM\ManyToOne(targetEntity=Offres::class, inversedBy="images")
     */
    private $offres;

      /**
     * @ORM\ManyToOne(targetEntity=Excursion::class, inversedBy="images")
     */
    private $excursion;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="images")
     */
    private $article;



 

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAgence(): ?Agence
    {
        return $this->agence;
    }

    public function setAgence(?Agence $agence): self
    {
        $this->agence = $agence;

        return $this;
    }

    public function getHotel(): ?Hotel
    {
        return $this->hotel;
    }

    public function setHotel(?Hotel $hotel): self
    {
        $this->hotel = $hotel;

        return $this;
    }

    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    public function setPays(?Pays $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getSites(): ?Sites
    {
        return $this->sites;
    }

    public function setSites(?Sites $sites): self
    {
        $this->sites = $sites;

        return $this;
    }

    public function getOffres(): ?Offres
    {
        return $this->offres;
    }

    public function setOffres(?Offres $offres): self
    {
        $this->offres = $offres;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    
    

    
}
