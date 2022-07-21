<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
#[ApiResource]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(inversedBy: 'panier', targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $proprietaire;

    #[ORM\OneToMany(mappedBy: 'panier', targetEntity: Commande::class, cascade: ['all'])]
    private $commandes;

    #[ORM\OneToMany(mappedBy: 'panier', targetEntity: Choix::class/*, cascade: ['all'], orphanRemoval: true*/)]
    private $listeDesChoix;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->listeDesChoix = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPanier(): ?int
    {
        return $this->idPanier;
    }

    public function setIdPanier(int $idPanier): self
    {
        $this->idPanier = $idPanier;

        return $this;
    }

    public function getProprietaire(): ?Utilisateur
    {
        return $this->proprietaire;
    }

    public function setProprietaire(Utilisateur $proprietaire): self
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }


    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setPanier($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getPanier() === $this) {
                $commande->setPanier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Choix>
     */
    public function getListeDesChoix(): Collection
    {
        return $this->listeDesChoix;
    }

    public function addListeDesChoix(Choix $listeDesChoix): self
    {
        if (!$this->listeDesChoix->contains($listeDesChoix)) {
            $this->listeDesChoix[] = $listeDesChoix;
            $listeDesChoix->setPanier($this);
        }

        return $this;
    }

    public function removeListeDesChoix(Choix $listeDesChoix): self
    {
        if ($this->listeDesChoix->removeElement($listeDesChoix)) {
            // set the owning side to null (unless already changed)
            if ($listeDesChoix->getPanier() === $this) {
                $listeDesChoix->setPanier(null);
            }
        }

        return $this;
    }

    //Vider le contenu du panier
    public function viderPanier():self
    {
        return $this;
    }

    /*Obtenir la valeur du panier
    public function valeurPanier():?float
    {
        
        return $valeurPanier;
    }*/
}
