<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Utilisateur;
use App\EventSubscriber\LogOutSubscriber;
use App\Repository\ProduitRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class PanierSaveOnLogOutTest extends ApiTestCase
{
    private $entityManager;

    protected function setUp() : void {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }
    
    /*
    public function testSomething(): void
    {
        $response = static::createClient()->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => '/']);
    }*/

    public function testLogOutSubscription()
    {
        //on s'assure que l'on s'abonne au bon evenement
        $this->assertArrayHasKey(LogoutEvent::class, LogOutSubscriber::getSubscribedEvents());
    }

    public function testPanierSaveOnLogOut()
    {
        //On instancie un client
        $client = static::createClient();

        //On le connecte

        
        //On remplie d'abord le panier
        $client->request('POST', '/add/1/3');
        $this->assertResponseIsSuccessful();
        //Recuperons la session ainsi que le contenu du panier
        $session = $client->getKernelBrowser()->getRequest()->getSession();
        $panier = $session->get("panier");

        $expectedPanier = [1 => 3];
        $this->assertEquals($expectedPanier, $panier);
        //On instancie l'eventsubscriber 

        $subscriber = new LogOutSubscriber($session, $this->entityManager);
        //On instancie l'evenement
        $token = $this->getMockBuilder(TokenInterface::class)->getMock();
        $event = new LogoutEvent(new Request(),$token);
        //On se deconnecte pour declencher l'evenement, pour cela il nous faut appeler la route de logout
        $client->request('GET', '/logout');



        //On met en place le dispatcher qui va gerer l'execution de l'eventsubscriber
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch($event);

        //$this->entityManager->flush();
        
        
        //On s'attend ensuite a ce que la base de données contient le panier contenant les produits sauvegardés
        $utilisateurRepository = $this->entityManager->getRepository(Utilisateur::class);
        $user131 = $utilisateurRepository->find(131);

        
        
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;

        
    }
}
