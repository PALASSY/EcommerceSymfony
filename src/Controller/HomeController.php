<?php
namespace App\Controller;

use App\Repository\FoodRepository;
use App\Service\Food\FoodDql;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{

  /**
   * La page HOME
   *  @Route("/", name="homepage")
   * @return Response
   */
  public function home(ObjectManager $manager, FoodDql $foodDql,FoodRepository $foodrepo)
  {

    $entree = $foodDql->entree();
    $plat = $foodDql->plat();
    $dessert = $foodDql->dessert();
    $foodImages = $foodDql->foodImages();
    $foods = $foodDql->allFoods();
    //dd($foodrepo->findBestFoods(3));
    

    //$img = array_map('current', $imagesPlats);
    //dd($ids);
      

    return $this->render('home.html.twig',['titre'=>'Page Home', 'entree'=>$entree,'plat'=>$plat,'dessert'=>$dessert,'foodImages'=>$foodImages, 'foods'=>$foods,'bestFoods'=>$foodrepo->findBestFoods(3)]);
  }


   

}