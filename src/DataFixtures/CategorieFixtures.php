<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CategorieFixtures extends Fixture implements DependentFixtureInterface
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
        //Creation des 5 categories principaux
        for ($i = 0; $i < 5; $i++) {
            $categorie = new Categorie();
            $categorie->setLibelle($faker->word());
            $categorie->setDescription($faker->paragraph());
            //On affecte des images au catégories
            $categorie->setCapture('https://agile-dawn-36258.herokuapp.com/images/categories/' . $image[$faker->numberBetween(0, count($image) - 1)]);
            $manager->persist($categorie);

            //On enregistre les 5 categories principaux en reference
            $this->addReference("categorie_" . $i, $categorie);
        }

        // Creation des 15 sous categories
        for ($i = 5; $i < 20; $i++) {
            $categorie = new Categorie();
            $categorie->setLibelle($faker->word());
            $categorie->setDescription($faker->paragraph());
            $categorie->setCapture('https://agile-dawn-36258.herokuapp.com/images/categories/' . $image[$faker->numberBetween(0, count($image) - 1)]);

            //Affectation du produit mere
            $categorieMere = $this->getReference('categorie_' . $faker->numberBetween(0, 4));
            $categorie->setCategorie($categorieMere);

            $manager->persist($categorie);
            //On enregistre les 5 categories principaux en reference
            $this->addReference("categorie_" . $i, $categorie);
        }

        //On affecte une catégorie à chaque produit
        for ($i = 0; $i < 300; $i++) {
            $produit = $this->getReference("produit_" . $i);
            $categorie = $this->getReference("categorie_" . $faker->numberBetween(5, 19));
            $produit->setCategorie($categorie);
        }



        //Enregistrons la categorie dans une reference de facon à pouvoir venir recuperer cette categorie, sans avoir à refaire des requetes complexes en base de données


        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProduitFixtures::class
        ];
    }
}
