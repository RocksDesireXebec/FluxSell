<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ProduitByStart;
use App\Controller\ProduitController;
use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ApiResource(collectionOperations: [
    'get',
    'get_most_popular' => [
        'method' => 'GET',
        'path' => '/mostpopular',
        'controller' => ProduitController::class,
        'read' => false,
        'pagination' => false,
    ],
    'get_start_betOneAndTwo' => [
        'method' => 'GET',
        'path' => '/promotions',
        'controller' => ProduitByStart::class,
        'read' => false,
        'pagination' => false,
    ],
])]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'guid')]
    private $idProduit;

    #[ORM\Column(type: 'string', length: 255)]
    private $libelle;

    #[ORM\Column(type: 'float')]
    private $prix;

    #[ORM\Column(type: 'integer')]
    private $etoile;

    #[ORM\Column(type: 'string', length: 255)]
    private $marque;

    #[ORM\Column(type: 'integer')]
    private $etat;

    #[ORM\Column(type: 'float')]
    private $note;

    #[ORM\Column(type: 'integer')]
    private $qteEnStock;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\Column(type: 'array')]
    private $informationsDetaillees = [];

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'produits')]
    private $categorie;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $capture;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProduit(): ?string
    {
        return $this->idProduit;
    }
    
    /**
     * setIdProduit
     *
     * @param  string $idProduit
     * @return self
     */
    public function setIdProduit(string $idProduit): self
    {
        $this->idProduit = $idProduit;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getEtoile(): ?int
    {
        return $this->etoile;
    }

    public function setEtoile(int $etoile): self
    {
        $this->etoile = $etoile;

        return $this;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): self
    {
        $this->marque = $marque;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(float $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getQteEnStock(): ?int
    {
        return $this->qteEnStock;
    }

    public function setQteEnStock(int $qteEnStock): self
    {
        $this->qteEnStock = $qteEnStock;

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


    public function getInformationsDetaillees(): ?array
    {
        return $this->informationsDetaillees;
    }

    public function setInformationsDetaillees(array $informationsDetaillees): self
    {
        $this->informationsDetaillees = $informationsDetaillees;

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

    public function getCapture(): ?string
    {
        return $this->capture;
    }

    public function setCapture(?string $capture): self
    {
        $this->capture = $capture;

        return $this;
    }

    
}
