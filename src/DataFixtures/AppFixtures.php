<?php

namespace App\DataFixtures;

use App\Entity\Commande;
use App\Entity\Food;
use App\Entity\Image;
use App\Entity\Reservation;
use App\Entity\Role;
use App\Entity\Table;
use App\Entity\User;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    //On va créer une variable privée GOBAL pour représenter le crypteur 
    private $encoder;

    //On va initialiser ce cryptage 
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        //Déclarer le faker et importer la class Factory
        $faker = Factory::create('FR-fr');

        //On va créer un nouvel Objet Role()
    //    $adminRole = new Role();
        //On va setter le titre de ce nouvel Objet() à ROLE_ADMIN, on garde le même ecriture dans UserInterface 
    //    $adminRole->setTitle('ROLE_ADMIN');
        //On va enregister 
    //    $manager->persist($adminRole);

        //On va créer un nouvel Objet User() avec un title(ROLE_ADMIN)
    //   $adminUser = new User();
        //On va setter toutes les données de ce nouvel Objet 
    //    $adminUser->setFirstname('PALASSY')
    //        ->setLastname('Christian')
    //        ->setEmail('christianpalassy@hotmail.fr')
    //        ->setAvatar('https://randomuser.me/api/portraits/men/44.jpg')
    //        ->setMdphashed($this->encoder->hashPassword($adminUser, 'password'))
    //        ->setIntroduction($faker->sentence())
    //        ->setDescription("<p>" . join("</p><p>", $faker->paragraphs(4)) . "</p>")
    //        ->addUserRole($adminRole);
    //    //On enregistre 
    //    $manager->persist($adminUser);


        //On va créer un tableau pour récupérér les données des Utilisateurs en mode GLOBAL
        $users = [];

        //On va mettre dans un tableau les options (homme/femme) pour conditionner le genre de l'Avatar
        $genres = ['female', 'male'];

        //Liste des places existantes pour l'Entity Table()
        $places = ['terrasse', 'salle'];

        //Catégorie de menu
        $category = ['entree', 'plat', 'dessert'];

        //Choix mode de paiement pour la commande de menu
        $choice = ['sepa', 'cb', 'cheque'];



        //On va créer 10 authors(Remarque: 1 author peut avoir plusieurs food())
    //    for ($i = 1; $i <= 10; $i++) {

            //On va préparer l'Avatar(randomuser.me)
            //on va randomiser les options de genre avec faker
    //        $genreAvatar = $faker->randomElement($genres);

            //On va distribuer un nombre entre 1 et 99 avec faker puis concaténer .jpg
    //        $avatarId = $faker->numberBetween(1, 99) . '.jpg';

            //On va prendre une partie de l'URL  de (randomuser.me)
    //        $avatar = 'https://randomuser.me/api/portraits/';

            //On va finaliser l'URL complet de l'Avatar avec la condition ternaire
    //        $avatar .= ($genreAvatar == 'male' ? 'men/' : 'women/') . $avatarId;

            //On va créér une description de l'utilisateur avec faker
    //        $description = "<p>" . join("</p><p>", $faker->paragraphs(5)) . "</p>";

            //On va créer un nouvel Objet User()
    //        $user = new User();

            //On va crypter le MDP de nouvel Objet User()
    //        $hash = $this->encoder->hashPassword($user, 'password');

            //On va setter le nouvel Obet User()
    //        $user->setFirstname($faker->firstname)
    //            ->setLastname($faker->lastname)
    //            ->setEmail($faker->email)
    //            ->setAvatar($avatar)
    //            ->setMdphashed($hash)
    //            ->setIntroduction($faker->sentence())
    //            ->setDescription($description)
                //->setSlug()
            ;

            //Il manque que : Slug, on va lui faire un Lifecycle Callbacks 

            //On va persister
    //        $manager->persist($user);

            //On va aussi peupler le tableau vide de $users(pour être ré-utiliser en mode GLOBAL)
    ////        $users[] = $user;
    ////    }



        //Créer 10 tables
    //    for ($t = 0; $t <= 10; $t++) {

            //On va créer une nouvelle Objet Table()
    //        $table = new Table();

            //Randomiser la place 
    //        $place = $faker->randomElement($places);
            //On Créer nombre de personne de 1 à 20
    //        $nombre = mt_rand(1, 8);
            //Créer la date de réservation 
    //        $date = $faker->dateTimeBetween('-3 months');
            //la created_at à faire en __construct dans Entity Table()
            //Le champ relationnel c'est le tableau User[] à faire un random
    //        $user = $users[mt_rand(0, count($users) - 1)];
            //Créer une image 
    //        $image = $faker->imageUrl(400, 350);

            //On va setter le nouvel Objet Table()
    //        $table->setPlace($place)
    //            ->setNombre($nombre)
    //            ->setDate($date)
    //            ->setImage($image)
    //            ->setAuthortable($user);

            //Enregistrer
    //        $manager->persist($table);



            //On va créer 5 reservations
    //        for ($r = 0; $r = mt_rand(0, 5); $r++) {
                //On va créer une Objet Reservation()
    //            $reservation = new Reservation();

                //Le client c'est l'user récupérer dans un tableau 
    //            $client = $users[mt_rand(0, count($users) - 1)];
                //La table c'est le nouvel Objet Table()
    //            $table = $table;
                //La date de réservation
    //            $date = $faker->dateTimeBetween('-3 months');
                //Le commentaire
    //            $comment = $faker->sentence();

                //On va setter 
    //            $reservation->setClient($client)
    //                ->setTabledisponible($table)
    //                ->setDate($date)
    //                ->setCommentaire($comment);
    //            $manager->persist($reservation);
    //        }
    //    }



        //Création 30 Food()
        for ($i = 1; $i < 30; $i++) {

            //Créer nouvel Food()
            $food = new Food();

            $menu = $faker->sentence();
            $description = "<p>" . join("</p><p>", $faker->paragraphs(5)) . "</p>";
            $image = $faker->imageUrl(1000, 350);

            //On va randomiser la catégory
            $categoryrandom = $faker->randomElement($category);

            //Comme on a rajouter un champ(author) relationnel(Food à User) alors il faut setter ce champ
            //On va randomiser les données des utilisateurs dans qu'on a récupérer dans un tableau GLOBAL
//            $user = $users[mt_rand(0, count($users) - 1)];

            $food->setMenu($menu)
                ->setPrice(mt_rand(3, 80))
                ->setDescription($description)
                ->setCoverImage($image)
                ->setStock(mt_rand(1, 400))
    //            ->setAuthor($user)
    //            ->setCategory($categoryrandom)
                ;
            $manager->persist($food);

            //Chaque Food() aura 3 à 5 images secondaires
        for ($j = 1; $j <= mt_rand(3, 5); $j++) {
            $image = new Image();
            $url = $faker->imageUrl();
            $caption = $faker->sentence();

            $image->setUrl($url)
                  ->setCaption($caption)
                  ->setFood($food)
                  ;
            $manager->persist($image);
        }



            //On va créer 5 commandes
    //        for ($k = 1; $k = mt_rand(0, 5); $k++) {

                //Créer une nouvelle commande
    //            $commande = new Commande();

                //Le commandeur c'est l'un des 10 authors
    //            $commandeur = $users[mt_rand(0, count($users) - 1)];

                //Le menu c'es le nouvel Objet Food()
    //            $menufood = $food;

                //La date de commande 
    //            $datecommande = $faker->dateTimeBetween('-3 months');

                //Le nombre de menu 
    //            $nbrmenu = mt_rand(1, 5);

                //Le prix total de la commande 
    //            $prixtotal = $food->getPrice() * $nbrmenu;

                //Mode de paiement 
    //            $modepaiement = $faker->randomElement($choice);

                //Etat de paiement 
    //            $etatpaiement = 1;

                //Date de livraison
    //            $datelivraison = $faker->dateTimeBetween('-2 days');

                //Lieu de livraison 
    //            $lieulivraison = $faker->address;

                //Il manque la date de création de commande, on le fera en construct dans l'Entity Commande()


                //On va setter le nouvel Objet Commande()
    //            $commande->setCommandeur($commandeur)
    //                ->setDatecommande($datecommande)
    //                ->setNbrmenu($nbrmenu)
    //                ->setPrixtotal($prixtotal)
    //                ->setModepaiement($modepaiement)
    //                ->setEtatpaiement($etatpaiement)
    //                ->setDatelivraison($datelivraison)
    //                ->setLieulivraison($lieulivraison)
                    //->setMenu($menufood)
    //            ;
    //            $manager->persist($commande);
    //        }
        }


        //On envoye la requête
        $manager->flush();
    }
}
