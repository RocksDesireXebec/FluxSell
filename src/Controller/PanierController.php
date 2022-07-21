<?php

namespace App\Controller;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class PanierController extends AbstractController
{
    #[Route('/add/{id}/{quantite}', name: 'app_panier_add')]
    public function add(Produit $produit, int $quantite, SessionInterface $session): JsonResponse
    {
        
        $qteEnStock = $produit->getQteEnStock();
        //dd($quantite);
        $id = $produit->getId();
        $panier = $session->get("panier",[]);
        
        if (!empty($panier[$id])) {
            // quantite desirée superieur a la qte present en stock
            if ($quantite >= $qteEnStock) {
                $panier[$id] = $qteEnStock;
            } else {
                $panier[$id] = $panier[$id] + $quantite;
            }
        } else {
            // quantite desirée superieur a la qte present en stock
            //dd($quantite);
            if ($quantite >= $qteEnStock) {
                $panier[$id] = $qteEnStock;
            } else {
                $panier[$id] = 0;
                $panier[$id] = $panier[$id] + $quantite;
            }
        }
        $session->set("panier",$panier);
        return $this->json($panier,$status = 200, $headers = [], $context = []);
    }

    #[Route('/remove/{id}/{quantite}', name: 'app_panier_remove')]
    public function remove(Produit $produit, $quantite, SessionInterface $session): JsonResponse
    {
        $id = $produit->getId();
        $panier = $session->get("panier",[]);
        if (!empty($panier[$id])) {
            if ($panier[$id]>$quantite) {
                $panier[$id] = $panier[$id] - $quantite;
            } else {
                unset($panier[$id]);
            }
        }
        return $this->json($panier,$status = 200, $headers = [], $context = []);
    }
}
