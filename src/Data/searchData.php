<?php
namespace App\Data;

class searchData
{
  /**
   * Recherche le mot clé
   *
   * @var string
   */
  public $q = "";


  /**
   * Tableau qui contient les catégory séléctionnés
   *
   * @var Category[]
   */
  public $category = [];


  /**
   * Recherche le prix minimum
   *
   * @var null|integer
   */
  public $min;

  /**
   * Recherche le prix max
   *
   * @var null|integer
   */
  public $max;



}
