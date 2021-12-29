<?php 
namespace App\Service\Pagination;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Pagination
{
  //On va créer des varibles privées les getter et setter

  //Il faut: l'Entity/la limite(par page)/La page actuelle(par défaut)/manager(pour ciblé l"entity)
  private $entityClass;
  private $limit = 10;
  private $currentPage;
  private $manager;

  //Il faut une @Route ciblée
  private $route;

  //Il faut récupérer l'environnement de twig
  private $twig;

  //Il faut: le template pour être setter ou getter
  private $templatePath;

  public function __construct(ObjectManager $manager,Environment $twig, RequestStack $request,$templatePath) {
    $this->manager = $manager;
    $this->twig = $twig;
    $this->route = $request->getCurrentRequest()->attributes->get('_route');
    //dd($this->route);
    $this->templatePath = $templatePath;
  }


  /**
   * Appeller le moteur de twig et la template à utiliser
   */
  public function display()
  {
    //dd($this->templatePath); ce chemin se trouve dans config/service.yaml
    $this->twig->display($this->templatePath,[
      'route' => $this->route,
      'page' => $this->currentPage,
      'totalOffset' => $this->getTotalOffset()
    ]);
  }



  //On va getter et setter (l'entity/la limite(par page)/la page actuelle(par défaut))

  public function getEntityClass()
  {
    return $this->entityClass;
  }

  public function getLimit()
  {
    return $this->limit;
  }

  public function getCurrentPage()
  {
    return $this->currentPage;
  }

  public function getRoute()
  {
    return $this->route;
  }

  public function getTemplatePath()
  {
    return $this->templatePath;
  }


  public function setEntityClass($entityClass)
  {
    $this->entityClass = $entityClass;
    return $this;
  }

  public function setLimit($limit)
  {
    $this->limit = $limit;
    return $this;
  }

  public function setCurrentPage($currentPage)
  {
    $this->currentPage = $currentPage;
    return $this;
  }

  public function setRoute($route)
  {
    $this->route = $route;
    return $this;
  }

  public function setTemplatePath($templatePath)
  {
    $this->templatePath = $templatePath;
    return $this;
  }

  

  /**
   * Function qui cible une donnée de l'entity grâce à findBy()
   *
   * @return Response
   */
  public function getData()
  {

    //Si l'Entity n'est pas renseigné, alors il y aura une erreur
    if (empty($this->entityClass)) {
      throw new \Exception("Le setEntityClass n'a pas été renseigné dans -C-");
    }

    //Caclul de l'offset(4e param)
    //La page actuelle x la limite - la limite
    $offset =  $this->currentPage * $this->limit - $this->limit;

    //Récupérer l'entity grâce à getRepository()
    $repo = $this->manager->getRepository($this->entityClass);

    //On va cibler une données de l'Entity
    $data = $repo->findBy([],[],$this->limit,$offset);

    return $data;
  }



  /**
   * Function qui calcule la total de l'offset(toutes les têtes de liste)
   *
   * @return Response
   */
  public function getTotalOffset()
  {
    //Récupérer l'entity grâce à getRepository()
    $repo = $this->manager->getRepository($this->entityClass);

    //Calculer la total des données de l'Entity
    $total = count($repo->findAll());

    //Calcul total de l'Offset
    //Total de données de l'entity / la limite
    $totalOffset = ceil($total/$this->limit);

    return $totalOffset;
  }

}
