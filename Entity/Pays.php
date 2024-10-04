<?php

namespace App\Entity;

use App\Repository\PaysRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=PaysRepository::class)
 * @UniqueEntity(fields={"nom"}, message="le nom du  pays déja existe")
 */
class Pays
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
    private $nom;

  

    /**
     * @ORM\OneToMany(targetEntity=Hotel::class, mappedBy="pays")
     */
    private $hotel;

    /**
     * @ORM\OneToMany(targetEntity=Sites::class, mappedBy="pays")
     */
    private $sites;

    /**
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="pays", orphanRemoval=true, cascade={"persist"})
     */
    private $images;


    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="pays")
     */
    private $articles;

  

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=Offres::class, mappedBy="pays")
   
     */
    private $offres;


    
 

    public function __construct()
    {
        $this->hotel = new ArrayCollection();
        $this->sites = new ArrayCollection();
        $this->images = new ArrayCollection();
       
        $this->articles = new ArrayCollection();
        $this->offres = new ArrayCollection();
     
  
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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
            $hotel->setPays($this);
        }

        return $this;
    }

    public function removeHotel(Hotel $hotel): self
    {
        if ($this->hotel->removeElement($hotel)) {
            // set the owning side to null (unless already changed)
            if ($hotel->getPays() === $this) {
                $hotel->setPays(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sites>
     */
    public function getSites(): Collection
    {
        return $this->sites;
    }

    public function addSite(Sites $site): self
    {
        if (!$this->sites->contains($site)) {
            $this->sites[] = $site;
            $site->setPays($this);
        }

        return $this;
    }

    public function removeSite(Sites $site): self
    {
        if ($this->sites->removeElement($site)) {
            // set the owning side to null (unless already changed)
            if ($site->getPays() === $this) {
                $site->setPays(null);
            }
        }

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
            $image->setPays($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getPays() === $this) {
                $image->setPays(null);
            }
        }

        return $this;
        
    }
    public function __toString() {
        if(is_null($this->nom)) {
            return 'NULL';
        }    
        return (string) $this->nom;
     }

  
    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setPays($this);
        } 

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getPays() === $this) {
                $article->setPays(null);
            }
        }

        return $this;
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

    /**
     * @return Collection<int, Offres>
     */
    public function getOffres(): Collection
    {
        return $this->offres;
    }

    public function addOffre(Offres $offre): self
    {
        if (!$this->offres->contains($offre)) {
            $this->offres[] = $offre;
            $offre->addPay($this);
        }

        return $this;
    }

    public function removeOffre(Offres $offre): self
    {
        if ($this->offres->removeElement($offre)) {
            $offre->removePay($this);
        }

        return $this;
    }

   
   
  
}
