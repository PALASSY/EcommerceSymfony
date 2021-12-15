<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Entity\Commande;
use App\Entity\Food;
use App\Form\AdressType;
use App\Repository\AdressRepository;
use App\Repository\CommandeRepository;
use App\Repository\FoodRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use App\Service\Commande\CommandeService;
use App\Service\Stripe\PaiementStripeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class CommandeController extends AbstractController
{
    /**
     * Page qui affiche  toutes les commandes en cours
     * @Route("/commande", name="commande_index")
     */
    public function index(CommandeService $service): Response
    {
        // Je récupère ma commande (menu => quantité menu)
        //Ma commande ressemble clé/valeur pas très parlant donc je vais créer un nouveau tableau pour enrichir ce couple clé/valeur 
        //Je vais récupérer toutes les clé/valeur et peupler mon nouveau tableau(menu=>un food)(quantity=>la quantité récupérer dans le panier)
        $commandelisible = $service->getFullCommande();

        //dd($commandelisible);

        //Je vais calculer également le montant total de tous les menus dans ma commande
        //Il est à zéro
        //Je vais récupérer tous les menus et quantités dans ma commande 
        //Le prix menu * la quantity
        //Puis je charge mon tableau total   
        $total = $service->getTotalCommande();

        $nombremenu = $service->getTotalnbrmenu();


        //On va retourner ce nouveau tableau
        return $this->render('commande/index.html.twig', [
            'commandelisible' => $commandelisible,
            'total' => $total,
            'nbrmenu' => $nombremenu
        ]);
    }



    /**
     * Page ajout menu dans commande
     * @Route("/commande/add/{id}", name="commande_add")
     *
     * @param [type] $id
     * @return Response
     */
    public function add($id, CommandeService $service)
    {
        //Récupérer ma commande : Un menu => nbr de menu
        //Si il y a déjà un menu dans ma commande(pas vide), on icrémente le nbr de menu
        //Mettre à jour la commande
        $service->add($id);

        return $this->redirectToRoute('commande_index');
    }


    
    /**
     * Page supprime menu dans commande
     * @Route("/commande/removemenu/{id}", name="commande_removemenu")
     *
     * @param [type] $id
     * @return Response
     */
    public function removemenu($id, CommandeService $service)
    {
        //Récupérer ma commande : Un menu => nbr de menu
        //Si il y a déjà un menu dans ma commande(pas vide), on décrémente le nbr de menu
        //Mettre à jour la commande
        $service->removemenu($id);

        return $this->redirectToRoute('commande_index');
    }





    /**
     * Page qui supprime une commande qui contient(menu => nbr menu)
     * @Route("/commande/remove/{id}", name="commande_remove")
     *
     * @param [type] $id
     * @return Response
     */
    public function remove($id, CommandeService $service)
    {
        //Récupérer ma commande : Un menu => nbr de menu
        //Si il y a un menu dans ma commande alors je le supprime
        //Mettre à jour ma commande 
        $service->remove($id);

        $this->addFlash("danger", "Votre commande N° {$id} a été supprimé");

        return $this->redirectToRoute('commande_index');
    }


    /**
     * Page qui affiche le panier 
     * @Route("/panier", name="panier")
     *
     * @param CommandeService $service
     * @return Response
     */
    public function getNbrMenuInCart(CommandeService $service)
    {
        $html = '';
        if (!empty($service->getFullCommande())) {
            $html .= $service->getTotalnbrmenu();
        }
        //dd($html);
        //echo $html;
        return $this->json($html);
    }



    /**
     * Page qui affiche l'adresse de livraison
     * @Route("/commande/adress", name="commande_showAdress")
     *
     * @param CommandeService $service
     * @return Response
     */
    public function showAdress(CommandeService $service)
    {
        
        if ($this->getUser()) {
            $commandes = $service->getFullCommande();
            $user = $this->getUser();
            if (empty($commandes)) {
                return $this->redirectToRoute('foods_list');
            } else {
                //Si il y a commande et user connecté, on récupère l'adresse
                    $adress = $user->getAdress();
                    $total = $service->getTotalCommande();
                    $nombremenu = $service->getTotalnbrmenu();

                    //dd($adress);
                    //dd($commandes);
            }
        } else {
            return $this->redirectToRoute('account_login');
        }
        return $this->render("commande/adress.html.twig", ['fullcommande' => $commandes, 'adress' => $adress,'total'=>$total,'nbrmenu'=>$nombremenu]);
    }



    /**
     * Page qui créer une adresse de livraison pour l'user connecté
     * @Route("/commande/createadress", name="commande_createAdress")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function createAdress(Request $request, ObjectManager $manager)
    {
        $adress = new Adress();
        $form = $this->createForm(AdressType::class, $adress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Indiquer que c'est l'author connécté qui a créé l'adresse
            $adress->setAdresseuser($this->getUser());

            $manager->persist($adress);
            $manager->flush();

            $this->addFlash("success", "Votre adresse de livraison a bien été enregistré");

            return $this->redirectToRoute("commande_showAdress");
        }
        return $this->render("commande/adressform.html.twig", ['form' => $form->createView()]);
    }




    /**
     * Paiment avec Stripe
     * @Route("/commande/paiementstripe/{id}", name="commande_paiementstripe")
     * @param [type] $id
     * @param UserRepository $userrepo
     * @return Response
     */
    public function paiementstripe($id,UserRepository $userrepo,CommandeService $service, PaiementStripeService $paiementStripe)
    {
        //Le client
        $user = $userrepo->findById($id);

        //dd($paiementStripe->paiementIntent());

        return $this->render("commande/paiementstripe.html.twig", ['user'=>$user,'fullcommande'=>$service->getFullCommande(), 'montanttotal'=>$service->getTotalCommande()]);
    }



    /**
     * Enregistrement de la commande dans BD
     * @Route("/commande/createcommande/{id}", name="commande_createcommande", methods="POST")
     * @param [type] $id
     * @param CommandeService $commande
     * @param Request $request
     * @param ObjectManager $manager
     * @return void
     */
    public function createCommande($id,CommandeService $service, PaiementStripeService $paiementStripe,Request $request, ObjectManager $manager, AdressRepository $adressRepo)
    {
        //Paramètre de stripe
        $commande = new Commande();
        $user = $this->getUser();

        $mailenvoye = $service->mail($user, $paiementStripe, $commande);

        if ($mailenvoye) {
            $this->addFlash("success","Votre commande a été enregistré avec succès, un récapitulatif de votre achat a été envoyé dans votre adresse mail");
        }
        
        return $this->redirectToRoute("commande_recapitulatif", ['id'=> $mailenvoye->getId()]);
    }



    /**
     * Récapitulatif de la commande avec stripe
     * @Route("/commande/recapitulatif/{id}", name="commande_recapitulatif")
     * @param [type] $id
     * @return Response
     */
    public function recapitulatifcommande($id, CommandeRepository $commadeRepo)
    {
        $commande = $commadeRepo->find($id);
        return $this->render("commande/recapitulatif.html.twig", ['commande'=>$commande]);
    }




    

}



