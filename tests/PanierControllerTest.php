<?php

namespace App\Tests;

//use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
class PanierControllerTest extends ApiTestCase
{   
    private $entityManager;

    protected function setUp() : void {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    //Produit existe dans la base de données et la quantité est suffisante
    public function testAjoutDeProduitEtQuantiteValideAuPanier(): void
    {
        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser
        //Ajout d'un produit et de sa quantite
        $client = static::createClient();
        $response = $client->request("POST","/add/1/2");
        $response = $client->request("POST","/add/1/2");
        $this->assertResponseIsSuccessful();
        $json = ["1"=> 4];
        $this->assertJsonEquals($json,$response->getContent());
        

        //réduire la quantite d'un exemplaire de produit valide, on renvoie la quantite present dans le panier moins le nombre passe en parametre
        
        $response = $client->request("POST","/remove/1/3");
        $this->assertResponseIsSuccessful();
        $json = ["1"=> 1];
        $this->assertJsonEquals($json,$response->getContent());

        
        //reduire la quantite d'un nombre superieur a celui present dans le panier, on suprime le produit
        $response = $client->request("POST","/remove/1/6");
        $this->assertResponseIsSuccessful();
        $json = [];
       
        $this->assertJsonEquals($json,$response->getContent());

        
        //Augmenter la quantite d'exemplaire d'un nombre superieur a celui present dans la base de données, en renvoie un json contenant la quantite maximale et le message precisant que c'est la limite est atteinte
        $response = $client->request("POST","/add/1/100");
        //dd($client->getKernelBrowser()->getResponse()->getContent());
        //dd($response->getContent());
        $this->assertResponseIsSuccessful();
        $json = ["1" => 7];
        $this->assertJsonEquals($json,$response->getContent());
        
        //Ajout d'un nouveau produit après un existant dejà au panier

        //Vider le panier

        //Supprimer un produit

        //Sauvegarde du panier en fin de session pour les utilisateurs ayant un compte (LogoutEvent)

        //Recuperation du panier dans la base de données après connection

        //Actualisation de la quantite en stock du produit après achat

        //Tests non fonctionels


        //Reduire la quantite d'exemplaire d'un produit qui n'est pas present dans le panier
        //Reduire d'un nombre negatif
        //Ajouter d'un nombre positifs
        /**
         * Penser dès que possible à creer plusieurs methodes
         */
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;

        
    }

}
