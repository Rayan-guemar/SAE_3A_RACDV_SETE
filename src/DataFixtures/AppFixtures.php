<?php

namespace App\DataFixtures;

use App\Entity\Festival;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // use the factory to create a Faker\Generator instance
        $faker = \Faker\Factory::create();
        for($i=0;$i<=10;$i++){
            $utilisateur = new Utilisateur();
            $utilisateur->setNom($faker->name());
            $utilisateur->setEmail($faker->email());
            $utilisateur->setPrenom($faker->name());
            $utilisateur->setMotDePasse("root");
        }
        /*
        for ($i=0;$i<=5;$i++){
            $festival = new Festival();
            $festival->setNomFestival($faker->sentence());
            $festival->setDescription($faker->text(500));
            $festival->setDateDebut($faker->dateTime());
            $festival->setDateFin($faker->dateTime($festival->getDateDebut()+3));
            $festival->setIdOrganisateur();
        }
        */


        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
