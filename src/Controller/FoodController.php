<?php

namespace App\Controller;

use App\Data\searchData;
use App\Entity\Food;
use App\Form\FoodType;
use App\Form\SearchType;
use App\Repository\FoodRepository;
use App\Service\Pagination\Pagination;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FoodController extends AbstractController
{
    /**
     * Page qui affiche tous les menus(plats/food...)
     * @Route("/foods/{page<\d+>?1}", name="foods_list")
     */
    public function index(FoodRepository $foodRepo,Request $request,$page,Pagination $pagination): Response
    {
        $data = new searchData();

        $form = $this->createForm(SearchType::class,$data);
        $form->handleRequest($request);
        //dd($data);

        //On va setter l'Entity et la page par défaut pour la pagination
        $pagination->setEntityClass(Food::class)
                   ->setCurrentPage($page)
                   ->setLimit(9)
                   ;

        if (!empty($foodRepo->findSearch($data))) {
            //Récupération des food par tri
            $foods = $foodRepo->findSearch($data);
            //dd($foods);..
        }else{
            $foods = $foodRepo->findAll();
            $this->addFlash("danger","Votre recherche n'a pas aboutit");
        }

        return $this->render('food/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);
    }


    /**
     * Page qui créer un nouvel Objet Food() (Plat/menu...)
     * @Route("/foods/new" , name ="foods_create")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function createFood(Request $request, ObjectManager $manager)
    {
        $food = new Food();
        $form = $this->createForm(FoodType::class, $food);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //On va setter toutes les images secondaires 
            foreach ($food->getImages() as $image) {
                $image->setFood($food);
                $manager->persist($image);
            }

            $manager->persist($food);

            //Indiquer que c'est l'author connécté qui a créé l'Objet Food()
            $food->setAuthor($this->getUser());

            $manager->flush();
            $this->addFlash("success", "Le menu <strong>{$food->getMenu()}</strong> est crée avec success");
            return $this->redirectToRoute("foods_single", ["slug" => $food->getSlug()]);
        }
        return $this->render("food/new.html.twig", ['form' => $form->createView()]);
    }


    /**
     * Page qui affiche un seul menu(plat/food...) par rapport à son ADN(slug)
     *  @Route("/foods/{slug}",name = "foods_single")
     * @param [type] $slug
     * @param Food $food
     * @return Response
     */
    public function showOneFood($slug, Food $food)
    {
        return $this->render("food/showOneFood.html.twig", [
            'food' => $food
        ]);
    }


    /**
     * Page qui modifie un seul repas(plat/food...)
     * @Route("/foods/{slug}/edit", name="foods_edit")
     * @Security("is_granted('ROLE_USER') and user === food.getAuthor()", message="Vous n'avez pas l'authorisation de modifier ce menu")
     * @param Food $food
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Food $food, Request $request, ObjectManager $manager)
    {
        //titre
        $titre = $food->getMenu();
        //On va créer un formulaire 
        $form = $this->createForm(FoodType::class, $food);
        $form->handleRequest($request);

        //Si soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //On vaa récupérer les images secondaires 
            foreach ($food->getImages() as $image) {
                //On va setter le menu 
                $image->setFood($food);
                //L'enregistrer 
                $manager->persist($image);
            }
            //Enregister le menu
            $manager->persist($food);
            //Enregistrer dans la BD 
            $manager->flush();
            //Message flash 
            $this->addFlash("success", "Le menu<strong>{$titre}</strong> a été modifié avec success");
            //Rediriger vers la page qui affiche un seul Food()
            return $this->redirectToRoute("foods_single", ["slug" => $food->getSlug()]);
        }
        return $this->render("food/edit.html.twig", ["form" => $form->createView()]);
    }


    /**
     * Page qui supprime le menu(grâce à son ADN(slug))
     * @Route("/foods/{slug}/delete", name="foods_delete")
     *
     * @param Food $food
     * @param ObjectManager $manager
     * @return void
     */
    public function delete(Food $food, ObjectManager $manager)
    {
        $title = $food->getMenu();

        $manager->remove($food);
        $manager->flush();

        $this->addFlash("success", "Votre menu: {$title} a été supprimer ");

        return $this->redirectToRoute("foods_list");
    }
}
