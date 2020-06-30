<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Evenements;

class EvenementFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i=1; $i<=10; $i++){

            $evenements =new Evenements();
            $evenements->setTitre("titre de l'evenement numero$i")
                      ->setContenu("<p>contenu de l'evenement $i</p>")
                      ->setVideo("http://placehold.it")
                      ->setDateCreation(new \DateTime());
            $manager->persist($evenements);

        }
        $manager->flush();
    }
}
