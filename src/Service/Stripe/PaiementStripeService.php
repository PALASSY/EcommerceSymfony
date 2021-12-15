<?php

namespace App\Service\Stripe;

use App\Entity\Commande;
use App\Service\Commande\CommandeService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class PaiementStripeService
{

  private $privateKey;
  private $service;
  protected $requestStack;

  public function __construct(CommandeService $service,RequestStack $requestStack) {
    
    $this->service = $service;
    $this->requestStack =$requestStack;
    
    //Si on est en mode dévéloppement
    if ($_ENV['APP_ENV'] === 'dev') {
      //On récupère la clé secret de stripe
      $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
    }
  }



  /**
   * Paiement Intent( à faire absolument)
   *
   * @return void
   */
    public function paiementIntent()
    {
    //Initialiser la clé secrète API stripe
    \Stripe\Stripe::setApiKey($this->privateKey);


    return \Stripe\PaymentIntent::create([
      'amount' => $this->service->getTotalCommande() * 100,
      'currency' => Commande::DEVISE,
      'automatic_payment_methods' => [
        'enabled' => true
      ]
    ]);
    }


 /**
  * Création Customer et Charge de Stripe, MAJ quantité de stock Food()
  *
  * @param [type] $user
  * @return Response
  */
  public function paiement($user)
  {
    
    //Initialiser la clé secrète API stripe
    \Stripe\Stripe::setApiKey($this->privateKey);

    //
    if (isset($_POST['stripeToken'])) {

      $request = $this->requestStack->getCurrentRequest();
      $source = $request->get('stripeToken');

      $customer =  \Stripe\Customer::create([
        'source' => $_POST['stripeToken'],
        'description' => "Commande de: ".$user->getFullname(),
        'email' => $user->getEmail()
      ]);

      $charge =   \Stripe\Charge::create([
        'amount' => $this->service->getTotalCommande() * 100,
        'currency' => Commande::DEVISE,
        'customer' => $customer->id
      ]);
      //dd($charge->values());
      

    //Si le paiement est validé
    if ($charge->status == 'succeeded') {
      
      //Mettre à jour la quantité(stock) de Food()
      $this->service->stock();

      //Récupérer tous les éléments stripe
      return $charge;
    }

    }
  }



  
  
 

}