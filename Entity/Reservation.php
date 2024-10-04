<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
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
    private $status;

    /**
     * @ORM\Column(type="string", length=255 ,nullable=true)
     */
    private $commentaire;

    /**
     * @ORM\Column(type="date")
     */
    private $dateCreation;

   
    /**
     * @ORM\ManyToOne(targetEntity=Offres::class, inversedBy="reservations")
     */
    private $offre;

    /**
     * @ORM\ManyToOne(targetEntity=Agent::class, inversedBy="reservations")
     */
    private $agent;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="reservations")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=GrilleTarifaire::class, inversedBy="reservations")
     */
    private $grilleTarifaire;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="reservations")
     */
    private $agence;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    

   

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateConfirmation(): ?\DateTimeInterface
    {
        return $this->dateConfirmation;
    }

    public function setDateConfirmation(\DateTimeInterface $dateConfirmation): self
    {
        $this->dateConfirmation = $dateConfirmation;

        return $this;
    }

    public function getOffre(): ?Offres
    {
        return $this->offre;
    }

    public function setOffre(?Offres $offre): self
    {
        $this->offre = $offre;

        return $this;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getGrilleTarifaire(): ?GrilleTarifaire
    {
        return $this->grilleTarifaire;
    }

    public function setGrilleTarifaire(?GrilleTarifaire $grilleTarifaire): self
    {
        $this->grilleTarifaire = $grilleTarifaire;

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

    public function __toString() {
        if(is_null($this->client)) {
            return 'NULL';
        }    
        return (string) $this->client;
     }
}
