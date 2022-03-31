<?php
namespace App\Service\Food;

use Doctrine\Persistence\ObjectManager;

class FoodDql
{
  private $manager ;
  

  public function __construct(ObjectManager $manager) {
    $this->manager = $manager;
  }
  

  /**
   * Récupérer tous les entree en mode random
   */
  public function entree()
  {
    $foodEntree = $this->manager->createQuery('SELECT f FROM App\Entity\Food f WHERE f.category = \'entree\' ')
      ->getResult();


    $foodEntree  = $foodEntree[array_rand($foodEntree)];
    return $foodEntree;

  }

  /**
   * Récupérer tous les plat en mode random
   */
  public function plat()
  {
    $foodPlat = $this->manager->createQuery('SELECT f FROM App\Entity\Food f WHERE f.category = \'plat\' ')
      ->getResult();

    $foodPlat  = $foodPlat[array_rand($foodPlat)];
    return $foodPlat;
  }

  /**
   * Récupérer tous les desserts en mode random
   */
  public function dessert()
  {
    $foodDessert = $this->manager->createQuery('SELECT f FROM App\Entity\Food f WHERE f.category = \'dessert\' ')
      ->getResult();

    $foodDessert  = $foodDessert[array_rand($foodDessert)];
    return $foodDessert;
  }


  /**
   * Récupérer tous les images de food en mode random
   */
  public function foodImages()
  {
    $foodImages = [];
    $foods = $this->manager->createQuery('SELECT f.coverImage FROM App\Entity\Food f')
      ->getResult();

    //$foods  = $foods[array_rand($foods)];
    $foods = array_intersect_key($foods, array_flip(array_rand($foods, 8)));

    foreach ($foods as $value) {
      # code..
      $foodImages[] .= $value['coverImage'];
    }
    return $foodImages;
  }



  /**
   * Récupérer tous les food
   *
   * @return Response
   */
  public function allFoods()
  {
    $foods = [];
    $food = $this->manager->createQuery('SELECT f FROM App\Entity\Food f')
      ->getResult();

    $foodsSelect = array_intersect_key($food, array_flip(array_rand($food, 8)));

    foreach ($foodsSelect as $value) {
      $foods[] = $value;
    }
      return $foods;
  }


}