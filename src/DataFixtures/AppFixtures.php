<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //Création des auteurs
        $listBook = [];
        $listAuthor = [];
        for($i = 0; $i<10; $i++)
        {
            $author = new Author;
            $author->setFirstName("Prenom ". $i);
            $author->setLastName("Nom de famille ". $i);
            $manager->persist($author);

            //Sauvegarde de l'auteur dans un tableau
            $listAuthor[]= $author;
        }

        //Création des livres
    
        for($i = 0; $i<20; $i++)
        {
            $book = new Book;
            $book->setTitle('Livre'. $i);
            $book->setCoverText('Quatrième de couverture numéro : '. $i);
            $book->setAuthor($listAuthor[array_rand($listAuthor)]);
            $manager->persist($book);

            //sauvegarde du livre dans un tableau
            $listBook[]= $book;
        }

        for($i = 0; $i<10; $i++) 
        {
            $author->addBook($listBook[$i]);
        }

        $manager->flush();
    }
}
