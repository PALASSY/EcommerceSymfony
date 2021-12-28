<?php

namespace App\Controller;

use App\Entity\Food;
use App\Form\FoodType;
use App\Repository\FoodRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminFoodController extends AbstractController
{
    /**
     * Page qui affiche tous les menus
     * @Route("/admin/foods", name="admin_foods_list")
     */
    public function index(FoodRepository $foodrepo): Response
    {
        return $this->render('admin/food/index.html.twig', [
            'foods' => $foodrepo->findAll()
        ]);
    }



    /**
     * Page qui affiche la modification d'un menu
     * @Route("/admin/food/{id}/edit", name="admin_foods_edit")
     *
     * @param Food $food
     * @param Request $request
     * @param ObjectManager $manager
     * @return void
     */
    public function edit(Food $food, Request $request,ObjectManager $manager)
    {
        $form = $this->createForm(FoodType::class,$food);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($food);
            $manager->flush();
            $this->addFlash("success","Votre menu a été modifié");
            return $this->redirectToRoute("admin_foods_list");
        }
        return $this->render("admin/food/edit.html.twig", [
            "food" => $food,
            "form" => $form->createView()
        ]);
    }



    /**
     * Page qui supprime un menu
     * @Route("/admin/foods/{id}/delete", name="admin_food_delete")
     * @param Food $food
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Food $food, ObjectManager $manager)
    {

       if ($food->getMenucommandeur() !== null) {
           $this->addFlash("warning","Vous ne pouvez pas supprimer une annonce qui possède une ou des commandes");
       }else {
           $menuid = $food->getId();
           $manager->remove($food);
           $manager->flush();
           $this->addFlash("danger","Le menu N° {$menuid} a été supprimé");
        }
       return $this->redirectToRoute("admin_foods_list");
    }
}
