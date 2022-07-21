<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Categorie;
use App\Entity\Produit;
use App\Repository\UtilisateurRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class AchatTest extends ApiTestCase
{
    //use RefreshDatabaseTrait;
    private $entityManager;
    private $serializer;

    protected function setUp() : void {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }


    public function testMainCategories(): void
    {
        $categorieRepository = $this->entityManager->getRepository(Categorie::class);
        //$nbCat = $categorieRepository->count([]);
        $mainCategories = $this->entityManager->createQuery("SELECT c FROM App\Entity\Categorie c WHERE c.categorie IS NULL")->getResult();
        //dd(count($mainCategories));
        //$this->assertEquals(20,$nbCat);

        $jsonMainCategories = $this->serializer->serialize($mainCategories,'json',[AbstractNormalizer::ATTRIBUTES => ['id','libelle','description']]);
    
        $response = static::createClient()->request('GET', '/mainCategories');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame(
            'content-type','application/ld+json;charset=utf-8'
        );
  
        
        $this->assertEquals($jsonMainCategories, $response->getContent());
        
    }

    public function testListeDesCategoriesDeCategorie() : void
    {
        $categorieRepository = $this->entityManager->getRepository(Categorie::class);
        $categorie3 = $categorieRepository->find(3);
        $categorie3Categories = $categorie3->getCategories();
        
        $jsonCategorie3Categories = $this->serializer->serialize($categorie3Categories,'json',[AbstractNormalizer::ATTRIBUTES => ['id','libelle','description']]);
        
        $response = static::createClient()->request('GET', '/categorie/3/categories');
        $this->assertResponseIsSuccessful();
        /*
        $this->assertResponseHeaderSame(
            'content-type','application/ld+json;charset=utf-8'
        );*/

        $this->assertEquals($jsonCategorie3Categories, $response->getContent());

    }

    public function testListeDesProduitsDeCategorie() : void
    {
        $categorieRepository = $this->entityManager->getRepository(Categorie::class);
        $categorie8 = $categorieRepository->find(8);
        $categorie8Produits = $categorie8->getProduits();
        
        $jsonCategorie8Produits = $this->serializer->serialize($categorie8Produits,'json',[AbstractNormalizer::ATTRIBUTES => ['id', 'idProduit','libelle','prix','etoile','marque','etat','note','qteEnStock','description']]);
       
        $response = static::createClient()->request('GET', '/categorie/8/produits');
        $this->assertResponseIsSuccessful();
        /*
        $this->assertResponseHeaderSame(
            'content-type','application/ld+json;charset=utf-8'
        );*/

        $this->assertEquals($jsonCategorie8Produits, $response->getContent());

    }

    public function testProduitDeCategorie() : void
    {
        $produitRepository = $this->entityManager->getRepository(Produit::class);
        $produit46 = $produitRepository->find(46);
        
        $jsonProduit46 = $this->serializer->serialize($produit46,'json',[AbstractNormalizer::ATTRIBUTES => ['id', 'idProduit','libelle','prix','etoile','marque','etat','note','qteEnStock','description']]);
        

        $response = static::createClient()->request('GET', '/categorie/8/produits/46');
        $this->assertResponseIsSuccessful();
        
        /*
        $this->assertResponseHeaderSame(
            'content-type','application/ld+json;charset=utf-8'
        );*/

        $this->assertEquals($jsonProduit46, $response->getContent());

    }

    public function testIntentSecretDeProduit() :void
    {
        $produitRepository = $this->entityManager->getRepository(Produit::class);
        $produit46 = $produitRepository->find(46);
        
        $jsonProduit46 = $this->serializer->serialize($produit46,'json',[AbstractNormalizer::ATTRIBUTES => ['id', 'idProduit','libelle','prix','etoile','marque','etat','note','description']]);
        
        $client = static::createClient();
        $response = $client->request('GET', '/intentStripe/46');
        $this->assertResponseIsSuccessful();
        $responseArray = $response->toArray();
        //$this->assertArrayHasKey('utilisateur',$response->getContent());
        $this->assertArrayHasKey('produit',$responseArray);
        $this->assertArrayHasKey('client_secret',$responseArray);

        $this->assertEquals($jsonProduit46, json_encode($responseArray['produit']));
        //$this->assertEquals($user,$response->getContent()['utilisateur']);
        $this->assertNotEmpty($responseArray['client_secret']);

    }
    /*
    public function testEnregistrementPaiementStripe()
    {
        $client = static::createClient();

        $response = $client->request(
            'POST',
            '/paiementStripe',
            [],
            [],
            [],
            $content = [
                'stripeIntentId' => $resultId,
                'stripeIntentPaymentMethod' => $resultatPaymentMethod,
                'stripeIntentStatus' => $resultatStatus,
                'subscription' => $idProduit
            ],
            true
        );

        $this->assertResponseIsSuccessful();
        $this->assertEquals("Le paiement a bien été enregistré",$response->getContent());
    }

    */


    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;

        
    }
}
