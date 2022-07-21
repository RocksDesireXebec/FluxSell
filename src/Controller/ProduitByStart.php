<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ProduitByStart extends AbstractController
{
    private $produitRepository;

    public function __construct(ProduitRepository $produitRepository) {
        $this->produitRepository = $produitRepository;
    }

    public function __invoke() : mixed
    {    
        return $this->produitRepository->startBetOneAndTwo();
    }

}
