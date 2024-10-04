<?php

namespace App\Entity;

use App\Repository\GrilleTarifaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GrilleTarifaireRepository::class)
 *  
 */
class GrilleTarifaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="date")
     */
    private $dateFin;

    /**
     * @ORM\Column(type="string", length=255  ,nullable=true)
     */
    private $typeChambre;

  

    /**
     * @ORM\ManyToOne(targetEntity=Offres::class, inversedBy="grilleTarifaires")
     */
    private $offre;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="grilleTarifaire")
     */
    private $reservations;

    /**
     * @ORM\ManyToMany(targetEntity=Hotel::class, inversedBy="grilleTarifaires")
     * @ORM\JoinColumn(nullable=true)
     */
    private $hotel;

    /**
     * @ORM\Column(type="float"  ,nullable=true)
     */
    private $prix_enfant;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->hotel = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getTypeChambre(): ?string
    {
        return $this->typeChambre;
    }

    public function setTypeChambre(string $typeChambre): self
    {
        $this->typeChambre = $typeChambre;

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
      public function __toString() {
        if(is_null($this->description)) {
            return 'NULL';
        }    
         return (string) $this->description;
     }

     //public function __toString() {
   
      //  return (string) $this->dateDebut->format('Y-m-d ');
   // }

    
    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setGrilleTarifaire($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getGrilleTarifaire() === $this) {
                $reservation->setGrilleTarifaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Hotel>
     */
    public function getHotel(): Collection
    {
        return $this->hotel;
    }

    public function addHotel(Hotel $hotel): self
    {
        if (!$this->hotel->contains($hotel)) {
            $this->hotel[] = $hotel;
        }

        return $this;
    }

    public function removeHotel(Hotel $hotel): self
    {
        $this->hotel->removeElement($hotel);

        return $this;
    }

    public function getPrixEnfant(): ?float
    {
        return $this->prix_enfant;
    }

    public function setPrixEnfant(float $prix_enfant): self
    {
        $this->prix_enfant = $prix_enfant;

        return $this;
    }
}
