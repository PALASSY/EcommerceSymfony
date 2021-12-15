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
   * Le prix minimum
   *
   * @var null|integer
   */
  public $min;

  /**
   * Le prix max
   *
   * @var null|integer
   */
  public $max;



}
