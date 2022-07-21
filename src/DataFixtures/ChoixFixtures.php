<?php

namespace App\DataFixtures;

use App\Entity\Choix;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ChoixFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        //Creation de 500 choix
        for ($i=0; $i < 500; $i++) { 
            $choix = new Choix();
            $choix->setQuantite($faker->numberBetween(1,20));
            $choix->setDateChoix(new \DateTimeImmutable());
            
            //Recuperation de tous les paniers de la base à  travers les references des utilisateurs et affectation des choix à ces panier
            $choix->setPanier( ($this->getReference("utilisateur_".$faker->numberBetween(0,149)))->getPanier() ); 

            //Recuperation de tous les produits pour faire affecter les choix à ces produits
            $choix->setProduit( $this->getReference('produit_'.$faker->numberBetween(0,299)) );

            $manager->persist($choix);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UtilisateurFixtures::class,
            ProduitFixtures::class
        ];
    }
}
