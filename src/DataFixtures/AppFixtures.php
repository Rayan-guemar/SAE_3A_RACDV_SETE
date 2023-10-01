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

//        for($i=0;$i<=10;$i++){
//            $utilisateur = new Utilisateur();
//            $utilisateur->setRoles(["bénévole"]);
//            $utilisateur->setNom($faker->lastName());
//            $utilisateur->setEmail($faker->email());
//            $utilisateur->setPrenom($faker->firstName());
//            $utilisateur->setPassword("root");
//            $manager->persist($utilisateur);
//        }

        for ($i=0;$i<=5;$i++){
            $festival = new Festival();
            $festival->setNom($faker->sentence());
            $festival->setDateDebut($faker->dateTime());
            $festival->setDateFin($faker->dateTime());
            $festival->setIdOrganisateur((new Utilisateur())
                                        ->setRoles(["orga"])
                                        ->setNom($faker->lastName())
                                        ->setEmail($faker->email())
                                        ->setPrenom($faker->firstName())
                                        ->setPassword("root")
        );
            $manager->persist($festival);
        }


        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
