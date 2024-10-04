<?php

namespace App\Entity;

use App\Repository\OffresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OffresRepository::class)
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 * "offre" = "App\Entity\Offres",
 *  "omra" = "App\Entity\Omra",
 *  "randonnee" = "App\Entity\Rondonnee",
 * "croisiere" = "App\Entity\Croisiere",
 * "excursion" = "App\Entity\Excursion",
 *  "voyageorganiser" = "App\Entity\VoyageOrganiser"})
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="offres", indexes={@ORM\Index(columns={"titre"}, flags={"fulltext"})})
 * 
 */
 
class Offres
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
     * @ORM\Column(type="text")
     */
    private $inclus;

    /**
     * @ORM\Column(type="text")
     */
    private $Non_Inclus;

    /**
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="offres", orphanRemoval=true, cascade={"persist"})
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="offres")
     */
    private $agence;

  
   
    /**
     * @ORM\OneToMany(targetEntity=GrilleTarifaire::class, mappedBy="offre")
     */
    private $grilleTarifaires;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="offre")
     */
    private $reservations;

   
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalRate;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\ManyToMany(targetEntity=Pays::class, inversedBy="offres")
     */
    private $pays;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="offres")
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=255 , nullable=true)
     */
    private $statut;

    /**
     * @ORM\OneToMany(targetEntity=Avis::class, mappedBy="offre")
     */
    private $avis;


   
    
  
   
    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->grilleTarifaires = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->pays = new ArrayCollection();
        $this->avis = new ArrayCollection();
  
     
  
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

   

    public function getInclus(): ?string
    {
        return $this->inclus;
    }

    public function setInclus(string $inclus): self
    {
        $this->inclus = $inclus;

        return $this;
    }

    public function getNonInclus(): ?string
    {
        return $this->Non_Inclus;
    }

    public function setNonInclus(string $Non_Inclus): self
    {
        $this->Non_Inclus = $Non_Inclus;

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setOffres($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getOffres() === $this) {
                $image->setOffres(null);
            }
        }

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

   

   
  

    /**
     * @return Collection<int, GrilleTarifaire>
     */
    public function getGrilleTarifaires(): Collection
    {
        return $this->grilleTarifaires;
    }

    public function addGrilleTarifaire(GrilleTarifaire $grilleTarifaire): self
    {
        if (!$this->grilleTarifaires->contains($grilleTarifaire)) {
            $this->grilleTarifaires[] = $grilleTarifaire;
            $grilleTarifaire->setOffre($this);
        }

        return $this;
    }

    public function removeGrilleTarifaire(GrilleTarifaire $grilleTarifaire): self
    {
        if ($this->grilleTarifaires->removeElement($grilleTarifaire)) {
            // set the owning side to null (unless already changed)
            if ($grilleTarifaire->getOffre() === $this) {
                $grilleTarifaire->setOffre(null);
            }
        }

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
            $reservation->setOffre($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getOffre() === $this) {
                $reservation->setOffre(null);
            }
        }

        return $this;
    }

    public function getTotalRate(): ?float
    {
        return $this->totalRate;
    }

    public function setTotalRate(?float $totalRate): self
    {
        $this->totalRate = $totalRate;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

 
    public function __toString() {
        if(is_null($this->titre)) {
            return 'NULL';
        }    
        return (string) $this->titre;
     }

    /**
     * @return Collection<int, Pays>
     */
    public function getPays(): Collection
    {
        return $this->pays;
    }

    public function addPay(Pays $pay): self
    {
        if (!$this->pays->contains($pay)) {
            $this->pays[] = $pay;
        }

        return $this;
    }

    public function removePay(Pays $pay): self
    {
        $this->pays->removeElement($pay);

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): self
    {
        if (!$this->avis->contains($avi)) {
            $this->avis[] = $avi;
            $avi->setOffre($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): self
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getOffre() === $this) {
                $avi->setOffre(null);
            }
        }

        return $this;
    }

    
   
}
