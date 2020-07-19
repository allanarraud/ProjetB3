<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Evenements;
use App\Entity\Categories;
use App\Entity\Commentaires;

class EvenementFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        for($i=0;$i<=3;$i++){
            $categorie =new Categories();
            $categorie->setNom($faker->sentence())
                      ->setDescription($faker->paragraph());
                      $manager->persist($categorie);

                      for($j=1; $j< mt_rand(4, 6); $j++){

                        $evenements =new Evenements();
                        
                        $contenu = '<p>' . join($faker->paragraphs(5),'</p><p>') .
                        '</p>';
                        $evenements->setTitre($faker->sentence())
                                  ->setContenu($contenu)
                                  ->setVideo('https://www.youtube.com/embed/e5udJTjbYzw')
                                  ->setDateCreation($faker->dateTimeBetween('-6 months'))
                                  ->setCategories($categorie);
                        $manager->persist($evenements);
                        
                        for($k=1; $j< mt_rand(4, 10); $k++){
                            $commentaire =new Commentaires();
                            $content = '<p>' .join($faker->paragraphs(2),'</p><p>').
                        '</p>';
                        $days=(new \DateTime())->diff($evenements->getDateCreation())->days;
                        
                            $commentaire->setContenu($content)

                                    ->setEmail($faker->email())
                                    ->setPseudo($faker->name())
                                    ->setRgpd(true)
                                    ->setActif(true)
                                    ->setDateCommentaire($faker->dateTimeBetween('-' .
                                    $days . 'days'))
                                    ->setEvenements($evenements);

                                    $manager->persist($commentaire);


                        }
            
                    }

        }
       
        $manager->flush();
    }
}
