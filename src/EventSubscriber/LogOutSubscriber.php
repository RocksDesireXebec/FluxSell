<?php

namespace App\EventSubscriber;

use App\Entity\Choix;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogOutSubscriber extends AbstractController implements EventSubscriberInterface
{
    
    private $entityManager;
    private $tokenStorage;
    private $jwtManager;

    public function __construct(EntityManager $entityManager = null, TokenStorageInterface $tokenStorage, JWTTokenManagerInterface $jwtManager) {
        
        /*
        if ($session == null) {
            $this->session = (new RequestStack())->getSession();
        } else {
            
             //Ceci pour les cas de tests
            
            $this->session = $session;
        }*/
        
        $this->jwtManager = $jwtManager;
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    
    public function onLogoutEvent(LogoutEvent $event)
    {
        
        //Vu que l'on a pour l'instant pas de panier pour étant donnee que l'utilisateur n'est pas authentifié on recupere ici un panier de la base de données
        //On recupere l'utilisateur connecté
        //$decodedJwtToken = $this->jwtManager->decode($this->tokenStorage->getToken());

        //dd($decodedJwtToken);

        //Recuperation du service requete
        //$session = $this->container->get('request_stack')->getSession();
        
        //dd($session);

        if (!$this->container->has('security.token_storage')) {
            
            throw new \LogicException('The Security Bundle is not registered in your application.');
        }
        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
    
            return;
        }
        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }
        dd($user);

        //$panierdb = $panierRepository->find(121);

        //On fait de même ici en recuperant l'un des utilisateurs de la bd
        //$user131 = $this->entityManager->find(Utilisateur::class,131);
        //$panierUser131 = $user131->getPanier();
        //$user131->setNom("RocksDXebec");
        
        //Ici on insere le code qui sauvegardera le panier de session
        
        //$session = $this->requestStack->getSession();
        //dd($session);
        //$panier = $this->session->get('panier',[]);
        //foreach ($panier as $idProduit => $quantite) {
        //    $produit = $this->entityManager->find(Produit::class,$idProduit);
            
        //    $choix = new Choix();
        //    $choix->setDateChoix(new \DateTimeImmutable());
        //    $choix->setQuantite($quantite);
        //    $choix->setProduit($produit);

        //    $panierUser131->getListeDesChoix()->add($choix);
        //    $choix->setPanier($panierUser131);

            

        //    $this->entityManager->persist($choix);
        //    $this->entityManager->flush();
        //    try {
        //    } catch (\PDOException $e) {
        //        dd($e->getMessage());
        //    }

        //}
    }
    
    
    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => [
                ['onLogoutEvent', 10]
            ],
        ];
    }
    
}
