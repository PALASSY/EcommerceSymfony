<?php 


namespace App\Service\Statistic;

use Doctrine\Persistence\ObjectManager;

class Statistics  
{
  private $manager;

  public function __construct(ObjectManager $manager) {
    $this->manager = $manager;
  }


  /**
   * Récupère toutes les résultats dans compact()
   *
   * @return Response
   */
  public function getStatistics()
  {
    $users = $this->getUsersCount();
    $foods = $this->getFoodsCount();
    $reservations = $this->getReservationsCount();
    $commandes = $this->getCommandesCount();
    $comments = $this->getCommandesCount();

    return compact('users','foods','reservations','commandes','comments');
  }



  /**
   * Récupère le nombre des utilisateurs
   *
   * @return Response
   */
  public function getUsersCount()
  {
    return $this->manager->createQuery(
      'SELECT count(u) FROM App\Entity\User u'
    )->getSingleScalarResult();
  }

   /**
   * Récupère le nombre des foods
   *
   * @return Response
   */
  public function getFoodsCount()
  {
    return $this->manager->createQuery(
      'SELECT count(f) FROM App\Entity\Food f'
    )->getSingleScalarResult();
  }

   /**
   * Récupère le nombre des réservation
   *
   * @return Response
   */
  public function getReservationsCount()
  {
    return $this->manager->createQuery(
      'SELECT count(r) FROM App\Entity\Reservation r'
    )->getSingleScalarResult();
  }

   /**
   * Récupère le nombre des commande
   *
   * @return Response
   */
  public function getCommandesCount()
  {
    return $this->manager->createQuery(
      'SELECT count(c) FROM App\Entity\Commande c'
    )->getSingleScalarResult();
  }

   /**
   * Récupère le nombre des comments
   *
   * @return Response
   */
  public function getCommentsCount()
  {
    return $this->manager->createQuery(
      'SELECT count(c) FROM App\Entity\Comment c'
    )->getSingleScalarResult();
  }



  public function getFoodStats($direction)
  {
    return $this->manager->createQuery(
      'SELECT AVG(c.rating) as note,
      f.menu,
      u.firstname,
      u.lastname,
      u.avatar 
      FROM App\Entity\Comment c
      JOIN c.food f
      JOIN f.author u
      GROUP BY f
      ORDER BY note ' .$direction)
      ->setMaxresults(5)
      ->getResult();

  }




}
