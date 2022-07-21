<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ApiResource(
    collectionOperations: ['get','post'],
    itemOperations: [
        'put',
        'patch',
        'delete',
        'get' => [
            'controller' => NotFoundAction::class,
            'openapi_context' => [
                'summary' => 'hidden',
            ],
            'read' => false,
            'output' => false
        ]
    ],
)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $Quantite;

    #[ORM\Column(type: 'datetime_immutable')]
    private $DateAchat;

    #[ORM\Column(type: 'float')]
    private $Montant;

    #[ORM\Column(type: 'boolean')]
    private $validite;

    #[ORM\Column(type: 'string', length: 255)]
    private $devise;

    #[ORM\Column(type: 'integer')]
    private $idMoyenPaiement;

    #[ORM\ManyToOne(targetEntity: Panier::class, inversedBy: 'commandes', cascade: ['refresh','persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $panier;

    #[ORM\Column(type: 'string', length: 255)]
    private $reference;

    #[ORM\OneToOne(mappedBy: 'commande', targetEntity: StripePayment::class, cascade: ['persist', 'remove'])]
    private $stripePayment;

    #[ORM\ManyToOne(targetEntity: Produit::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $produit;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getQuantite(): ?int
    {
        return $this->Quantite;
    }

    public function setQuantite(int $Quantite): self
    {
        $this->Quantite = $Quantite;

        return $this;
    }

    public function getDateAchat(): ?\DateTimeImmutable
    {
        return $this->DateAchat;
    }

    public function setDateAchat(\DateTimeImmutable $DateAchat): self
    {
        $this->DateAchat = $DateAchat;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->Montant;
    }

    public function setMontant(float $Montant): self
    {
        $this->Montant = $Montant;

        return $this;
    }

    public function getValidite(): ?bool
    {
        return $this->validite;
    }

    public function setValidite(bool $validite): self
    {
        $this->validite = $validite;

        return $this;
    }

    public function getDevise(): ?string
    {
        return $this->devise;
    }

    public function setDevise(string $devise): self
    {
        $this->devise = $devise;

        return $this;
    }

    public function getIdMoyenPaiement(): ?int
    {
        return $this->idMoyenPaiement;
    }

    public function setIdMoyenPaiement(int $idMoyenPaiement): self
    {
        $this->idMoyenPaiement = $idMoyenPaiement;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): self
    {
        $this->panier = $panier;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getStripePayment(): ?StripePayment
    {
        return $this->stripePayment;
    }

    public function setStripePayment(StripePayment $stripePayment): self
    {
        // set the owning side of the relation if necessary
        if ($stripePayment->getPanier() !== $this) {
            $stripePayment->setPanier($this);
        }

        $this->stripePayment = $stripePayment;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }
}
