<?php
    
namespace App\Services;

use App\Entity\Produit;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeService {
    private $privateKey;

    public function __construct(){
        if ($_ENV['APP_ENV'] === 'test') {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
        } else {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
        }
        
    }

    /**
     * Pour associer le prix du produit à une clé
     */

    public function paymentIntent(Produit $produit){
        $stripe = new Stripe();
        $stripe->setApiKey($this->privateKey);
        $paymentIntent = new PaymentIntent();
        return $paymentIntent->create([
            //Ici on precise le prix du produit avec l'attribut amount
            'amount' => $produit->getPrix()*100,
            'currency' => 'eur',
            'payment_method_types' => ['card']
        ]);
        
    }
    /**
     * Gerer le paiement et l'associer au back office de stripe
     */
    public function paiement(
        $amount,
        $currency,
        $description,
        array $stripeParameter
    ){
        $stripe = new Stripe();
        $stripe->setApiKey($this->privateKey);
        $payment_intent = null;
        // S'il y a eu intention de paiement
        if (isset($stripeParameter['stripeIntentId'])) {
            # Il n'entre pas dans cette condition
            $payment_intent = new PaymentIntent();
            $payment_intent = $payment_intent->retrieve($stripeParameter['stripeIntentId']);
        }

        //On verifie si le status a retourne succeeded
        if ($stripeParameter['stripeIntentStatus'] === 'succeeded') {
            # code...
        }else {
            $payment_intent->cancel();
        }
        //On declenche le paiement et il est redirige vers la plateforme stripe
        return $payment_intent;
    }


    /**
     * Recuperer le résultat de toutes les opérations provenant de stripe
     */
    public function stripe(array $stripeParameter, Produit $produit){
        return $this->paiement(
            $produit->getPrix()*100,
            "eur",
            $produit->getLibelle(),
            $stripeParameter

        );
    }
}   