<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\PaiementStripeType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class PaiementStripeController extends AbstractController
{   /** Cette Route recupere le prix via formulaire afin de creer l'intention d'achat
     */
    #[Route('/paiementstripe', name: 'app_paiement_stripe')]
    public function index(Request $request, ProduitRepository $produitRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        //Produit de test
        $produitTest = new Produit();
        $produitTest->setIdProduit("1");
        $produitTest->setLibelle("IPHONE-12");
        $produitTest->setPrix(2000);
        $produitTest->setEtoile(4);
        $produitTest->setMarque("IPHONE");
        $produitTest->setDescription("Bon");
        $produitTest->setEtat(3);
        $produitTest->setInformationsDetaillees([]);
        $produitTest->setNote(3.5);
        $produitTest->setQteEnStock(56);

        //Declaration de l'intention de paiement
        $intentSecret = $produitRepository->intentSecret($produitTest);

        $form = $this->createForm(PaiementStripeType::class);
        $form ->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           
            //Il a accepter de payer maintenant on enregistre ici la transaction dans la base de données
            $user = $this->getUser();
            if ($request->getMethod() === "POST") {
                //Recuperation des informations liées au résultats du  traitement de le transaction par le client
                $resource = $produitRepository->stripe($_POST, $produitTest);
                
                if($resource !== null){
                    $produitRepository->create_subscription($resource, $produitTest, $user, 1, "STRIPE");
                    return $this->redirectToRoute('app_paiement_stripe');
                }
                
                return $this->redirectToRoute('app_paiement_stripe');
                
            }
            return $this->redirectToRoute('app_paiement_stripe');
        }
        
        
        return $this->render('paiement_stripe/index.html.twig', [
            'controller_name' => 'PaiementStripeController',
            'user' => $this->getUser(),
            'product' => $produitTest,
            //Renvoie du resultat de l'intention de paiment au client contenant le client_secret permettant de valider le paiement
            'intentSecret' => $intentSecret,
            'PaiementStripeForm' => $form->createView()

        ]);
    }

    #[Route('/intentStripe/{id}', name: 'app_intent_stripe',  methods: ['GET'])]
    public function intent(Produit $produit, ProduitRepository $produitRepository, SerializerInterface $serializer) : Response
    {
        //Declaration de l'intention de paiement
        $intentSecret = $produitRepository->intentSecret($produit);
        //$produit = $serializer->serialize($produit,'json',[AbstractNormalizer::ATTRIBUTES => ['id','idProduit','libelle','prix','etoile','marque','etat','note','description']]);
        
        $data = ['produit' => $produit,
                 'client_secret' => $intentSecret];

        $json = $serializer->serialize($data,'json',[AbstractNormalizer::ATTRIBUTES => ['id','idProduit','libelle','prix','etoile','marque','etat','note','description']]);
        
        return new Response($json, 200, ['content-type'=>'application/ld+json;charset=utf-8']);
    }

}
