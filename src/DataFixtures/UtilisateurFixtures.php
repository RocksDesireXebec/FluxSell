<?php

namespace App\DataFixtures;

use App\Entity\Panier;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;
class UtilisateurFixtures extends Fixture
{
    protected $passwordHasher;
    public function __construct(UserPasswordHasherInterface $userPasswordHasher) {
        $this->passwordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i=0; $i < 150; $i++) {
            $panier = new Panier(); 
            $utilisateur = new Utilisateur();
            $utilisateur->setPanier($panier);
            $utilisateur->setEmail($faker->email());
            $utilisateur->setNom($faker->firstName());
            // encode the plain password
            $utilisateur->setPassword(
                $this->passwordHasher->hashPassword(
                        $utilisateur,
                        'rocksdxebec'
                )
            );
            $utilisateur->setPrenom($faker->lastName());
            $utilisateur->setPaysDeResidence($faker->country());
            $utilisateur->setTelephone($faker->e164PhoneNumber());

            $manager->persist($utilisateur);

            //Enregistrement dans une reference
            $this->addReference("utilisateur_".$i,$utilisateur);
        }

        $manager->flush();
    }
    
}
