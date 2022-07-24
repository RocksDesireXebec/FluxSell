<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $image = [
            "maserati-gran-turismo-g083e72ca7_1280.jpg",
            "laptop-ge78aa30de_1280.jpg",
            "internet-search-engine-g2d26200e7_1280.jpg",
            "hangers-g9dd0ca68e_1280.jpg",
            "fashion-g3a53582e2_1280.jpg",
            "clothing-gbcea1ff50_1280.jpg",
            "car-gd478a1aad_1280.jpg",
            "car-g4ab1567bf_1280.jpg",
            "camera-g68462af3b_1280.jpg",
            "cabinet-gb50816000_1280.jpg",
            "apple-gfd2d10694_1280.jpg",
            "workspace-g7433e6817_1280.jpg",
            "woman-g42924ab7b_1280.jpg",
            "woman-g9aa66b1fe_1280.jpg",
            "wardrobe-ga0956fa17_1280.jpg",
            "shoes-gd45b3742a_1280.jpg",
            "shoes-gb161d8653_1280.jpg",
            "shoes-ga30027ad6_1280.png",
            "shoes-g9ff20c2d8_1280.jpg",
            "portrait-ga53849e06_1280.jpg"
        ];
        $faker = Faker\Factory::create('fr_FR');
        //On va inserer 300 produits
        for ($i=0; $i < 300; $i++) { 
            $produit = new Produit();

            $produit->setIdProduit($faker->uuid());
            $produit->setLibelle($faker->word());
            $produit->setPrix($faker->randomFloat(0,100,2000000));
            $produit->setEtoile($faker->numberBetween(0,5));
            $produit->setMarque($faker->company());
            $produit->setEtat($faker->numberBetween(0,3));
            $produit->setNote($faker->numberBetween(0,5));
            $produit->setQteEnStock($faker->numberBetween(0,100));
            $produit->setDescription($faker->paragraph());
            $produit->setCapture('https://agile-dawn-36258.herokuapp.com/images/produits/'.$image[$faker->numberBetween(0 , count($image)-1) ]);
            $manager->persist($produit);

            //On enregistre le produit dans une reference
            $this->addReference("produit_".$i,$produit);
        }


        $manager->flush();
    }
}
