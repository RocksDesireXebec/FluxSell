<?php

namespace App\Repository;

use App\Entity\Commande;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\StripePayment;
use App\Entity\Utilisateur;
use App\Services\StripeService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    private $stripeService;

    public function __construct(ManagerRegistry $registry, StripeService $stripeService)
    {
        parent::__construct($registry, Produit::class);
        //Probleme avec cette instruction : object could not be converted to string
        $this->stripeService = $stripeService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Produit $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Produit $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Produit[] Returns an array of Produit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Produit
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function intentSecret(Produit $produit)
    {
    
        $intent = $this->stripeService->paymentIntent($produit);
        return $intent['client_secret'] ?? null;
    }

    public function stripe(array $stripeParameter, Produit $produit)
    {
        $ressource = null;
        $data = $this->stripeService->stripe($stripeParameter, $produit);
        if ($data) {
            //Recuperation des informations provenant de la plateforme Stripe concernant le paiement
            $ressource = [
                'stripeBrand' =>$data['charges']['data'][0]['payment_method_details']['card']['brand'],
                'stripeLast4' =>$data['charges']['data'][0]['payment_method_details']['card']['last4'],
                'stripeId' =>$data['charges']['data'][0]['id'],
                'stripeStatus' =>$data['charges']['data'][0]['status'],
                'stripeToken' =>$data['client_secret']
            ];

        }

        return $ressource;
    }

    //Enregistrement en base de données de la commande
    public function create_subscription(array $resource, Produit $produit, Utilisateur $utilisateur, int $quantite, string $moyenDePaiement)
    {
        //On definit ici les informations de la commande
        $commande = new Commande();
        //Probleme avec les double identifiant de commande
        $commande->setIdCommande(3);

        $commande->setPanier($utilisateur->getPanier());
        //Il faut que l'utilisateur ai un panier
        //$commande->setPanier($utilisateur->getPanier);
        $commande->setQuantite($quantite);
        $commande->setDevise("eur");
        $commande->setProduit($produit);
        $commande->setMontant($produit->getPrix());
        //Probleme avec le prix : ca utilise la somme 100 au lieu du prix reel du produit
        $commande->setMontant($quantite*$produit->getPrix());
        $commande->setIdMoyenPaiement(1);
        $commande->setReference(uniqid(prefix:'', more_entropy:false));
        $commande->setValidite($resource['stripeStatus']);

        //Configuration du paiement stripe
        $stripePayment = new StripePayment();
        $stripePayment->setBrandStripe($resource['stripeBrand']);
        $stripePayment->setLast4Stripe($resource['stripeLast4']);
        $stripePayment->setIdChargeStripe($resource['stripeId']);
        $stripePayment->setStripeToken($resource['stripeToken']);
        $stripePayment->setStatusStripe($resource['stripeStatus']);
        $stripePayment->setCreatedAt(new \DateTimeImmutable());
        $stripePayment->setUpdateAt(new \DateTimeImmutable());


        $commande->setStripePayment($stripePayment);
        $commande->setDateAchat(new \DateTimeImmutable());

        //Enfin on fait un persist et un flush
        $this->_em->persist($commande);
        $this->_em->flush();
    }

    //Les produits les plus populaires
    public function mostPopularProducts()
    {
        return $this->_em->createQuery('SELECT DISTINCT p FROM App\Entity\Produit p WHERE p.note >= 5')->getArrayResult();
    }

    //Les produits ayant une etoile entre 1 et 2
    public function startBetOneAndTwo()
    {
        return $this->_em->createQuery('SELECT DISTINCT p FROM App\Entity\Produit p WHERE p.etoile = p.note')->getArrayResult();
    }
    
}
