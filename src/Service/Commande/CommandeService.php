<?php
namespace App\Service\Commande;


use PHPMailer\PHPMailer\SMTP;
use App\Repository\FoodRepository;
use PHPMailer\PHPMailer\Exception;

use PHPMailer\PHPMailer\PHPMailer;
use Doctrine\Persistence\ObjectManager;
use App\Service\Stripe\PaiementStripeService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;



class CommandeService{

    //On va faire un __construct() pour utiliser la SessionInterface et FoodRepository
    protected $session;
    protected $foodRepo;
    private $manager;
    private $flash;

    public function __construct(SessionInterface $session, FoodRepository $foodRepo, ObjectManager $manager,FlashBagInterface $flash ) {
      $this->session = $session;
      $this->foodRepo = $foodRepo;
      $this->manager = $manager;
      $this->flash = $flash;
    }



    //Récupérer toutes les commandes 
    public function getFullCommande(): array
    {
     //On va récupérer la commande 
      $commande = $this->session->get('menu',[]);

      //dd($this->session);

     //Puis que les commandes ressemblent à une paire de clé/valeur, pas très lisible
      //On va créer un nouveau tableau pour l'enrichir
     $commandelisible = [];

      //On va récupérer toutes les clé/valeur et les mettre dans le nouveau tableau
      foreach($commande as $id => $quantity){
        //On va peupler le nouveau tableau
        $commandelisible[] = [
          'food' => $this->foodRepo->find($id),
          'quantity' => $quantity
        ];
      }

      //dd($commandelisible);
      return $commandelisible;
    }



    //Gestion de stock Food()
    public function stock()
    {
      //Les commandes 
      $datacommande = $this->getFullCommande();

      foreach ($datacommande as $key => $value) {
        //Id commande
        $idCommande= ($value['food'])->getId();
        //Stock commande
        $stockCommande= ($value['food'])->getStock();
        //Quantité Commande
        $quantityCommande= ($value['quantity']);
        
        //Food() qui correspond à la commande
        $food = $this->foodRepo->findById($idCommande);
        
       
        foreach ($food as $value) {
          //Id Food()
          $idFood = $value->getId();
          //Stock Food()
          $stockFood = $value->getStock();

          if ($idFood == $idCommande) {
            if ($stockCommande > $quantityCommande || $stockCommande == $quantityCommande) {
              //Déduire stock Food() à la quantity commande
              $lasttockFood = $stockFood - $quantityCommande;
              //Setter
              $setStock = $value->setStock($lasttockFood);
              //Enregistrer 
              $this->manager->persist($setStock);
              $this->manager->flush();
            }else {
              $this->flash->add("danger","Nous sommes en rupture de stock");
            }
          }     
        }
      }
      //$result =  array_intersect($datacommande, $datafood);
    }



    //Calcul total global des commandes
    public function getTotalCommande(): float
    {
      //Initialiser le total à 0
      $total = 0;

      //Je vais récupérer Prix et Quantity de toutes les commandes afin de pouvoir calculer le total
      //Prix et Quantity se trouvent dans la function getfullcommande()
      foreach ($this->getfullcommande() as $item) {
        $total += $item['food']->getPrice() * $item['quantity'];
        //dd($total);
      }

      //Retourner le tableau
      return $total;
    }



    //récupérer la total des nombres de menus
    public function getTotalnbrmenu(): int
    {
      //Initialiser à zéro
      $total = 0;

      //Quantity se trouve dans la function getfullcommande()
      foreach ($this->getfullcommande() as $item) {

          $total += $item['quantity'];
          //dd($total);
      }

      return $total;
    }



    //Ajout menu dans commande 
    public function add($id)
    {
      //Commande et sa quantité
      $commande = $this->session->get('menu',[]);

      //Commande complet et sa quantité
       $food = $this->foodRepo->find($id);
       $foodquantity = $food->getStock();

      //On incremente le nbr de menu si il y en a déjà
      if (!empty($commande[$id])) {
        if ($commande[$id] < $foodquantity) {
          // Incrémenté la commande
          $commande[$id]++;
        }else {
          $this->flash->add("danger","Vous avez atteint la quantité maximale de notre stock");
        }
      }else{
        //On rajoute le nbr de menu à 1
        $commande[$id] = 1;
      }

      //On va MAJ la commande
      $this->session->set('menu',$commande);
    }


    //Suprimer menu dans commande
    public function removemenu($id)
    {
      //On va récupérer la commande 
      $commande = $this->session->get('menu',[]);

      //Vérifier si la commande n'est pas vide 
      if(!empty($commande[$id])){
        if ($commande[$id] > 1) {
          //On va décrémenter le menu
          $commande[$id]--;
        }else {
          unset($commande[$id]);
        }
      }else{
        $commande[$id] = 0;
      }

      //On MAJ la session
      $this->session->set('menu',$commande);
    }



    //Suprimer une commande entière
    public function remove($id)
    {
      //On va récupérer la commande
      $commande = $this->session->get('menu',[]);

      //Vérifier si la commande n'est vide
      if (!empty($commande[$id])) {
        unset($commande[$id]);
      }

      //Mettre à jour la commande
      $this->session->set('menu', $commande);
    }
    
    


    //Récupérer les valeur dans stripe afin de les enregistrer dans BD
    public function stripe($user,PaiementStripeService $stripeService,$commande)
    {

      $stripeParameter = $stripeService->paiement($user);
      $date = new \DateTime();
      $totalprice = $this->getTotalCommande();
      $cardCommande = $stripeParameter->payment_method_details->type;
      $etatpaiement = 1;
      $datelivraison = new \DateTime('+2days');
      $nbrMenu = $this->getTotalnbrmenu();
      $lieulivraison = null;
      
      
      $adress = $user->getAdress();
      $adressPostal = $adress->getAdressepostale();
      $complementadresse = $adress->getComplementadresse();
      $ville = $adress->getVille();
      $region = $adress->getRegion();
      $codepostale = $adress->getCodepostale();

      if ($complementadresse !== null) {
        $lieulivraison .= $adressPostal." ".$complementadresse." ".$ville." ".$codepostale." ". $region;
      }else{
        $lieulivraison .= $adressPostal." ".$ville." ". $codepostale. " " .$region;
      }

      if (!empty($stripeParameter)) {
        $commande->setCommandeur($user)
                 ->setDatecommande($date)
                 ->setPrixtotal($totalprice)
                 ->setModepaiement($cardCommande)
                 ->setEtatpaiement($etatpaiement)
                 ->setDatelivraison($datelivraison)
                 ->setNbrmenu($nbrMenu)
                 ->setLieulivraison($lieulivraison)
                 ;
        $this->manager->persist($commande);
        $this->manager->flush();
      }
      return $commande;
    }


  //Envoie un email de récapitulatif d'achat après paiement 
  public function mail($user,PaiementStripeService $stripeService,$commande)
  {
    $fullcommande = $this->getFullCommande();
    $commandestripe = $this->stripe($user, $stripeService,$commande);
    $idCommande = $commandestripe->getId();
    $userid = $user->getId();


    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
      //Server settings
      $mail->SMTPDebug = false;                           //Dédug de l'envoi de l'email
      $mail->isSMTP();                                    //Si l'email ne sera pas envoyé,on décommande cette ligne
      $mail->Host       = 'smtp.gmail.com';             //Set the SMTP server to send through
      $mail->SMTPAuth   = true;                           //Enable SMTP authentication
      $mail->Username   = 'formation31palassy@gmail.com';             //SMTP username
      $mail->Password   = 'Forma31P&&';                       //SMTP password
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;    //Enable implicit TLS encryption
      $mail->Port       = 465;                            //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
      

      //Recipients
      $mail->setFrom('palassylucas@gmail.com', 'Shangrila');
      $mail->addAddress('formation31palassy@gmail.com', $user->getFullname());     //Add a recipient
      //$mail->addAddress('ellen@example.com');               //Name is optional
      $mail->addReplyTo('palassylucas@gmail.com.com', 'Information-Shangrila');
      //$mail->addCC('cc@example.com');      //Ajout copie
      //$mail->addBCC('bcc@example.com');   //Copie cachée

      //Attachments
      //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
      //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

      //Content
      $mail->isHTML(true);                                  //Set email format to HTML
      $mail->Subject = 'Récapitulatif de votre commande';


      $html = "<style>
	type='text/css'
	body,
	html,
	.body {
		background: #f3f3f3 !important;
	}
</style>
<!-- move the above styles into your custom stylesheet -->

<spacer size='16'></spacer>

<container>
      
	<spacer size='16'></spacer>

	<row>
		<columns>
			<h1>Merci pour votre commande</h1>
			<p>Thanks for shopping with us! Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad earum ducimus, non, eveniet neque dolores voluptas architecto sed, voluptatibus aut dolorem odio. Cupiditate a recusandae, illum cum voluptatum modi nostrum.</p>

			<spacer size='16'></spacer>

			<callout class='secondary'>
				<row>
					<columns large='6'>
						<p>
							<strong>Payment Method</strong><br/>
							Dubloons
						</p>
						<p>
							<strong>Votre email</strong><br/>";
      $html .= $user->getEmail();
      $html .= "
						</p>
						<p>
							<strong>Commande N°</strong><br/></p>";
      $html .= $idCommande;
      $html .= "
</p>
          </columns>
          <columns large='6'>
            <p>
              <strong>Shipping Method</strong><br/>
              Boat (1&ndash;2 semaines)<br/>
              <strong>Adresse de livraison</strong><br/>";
              
        $html .= $commandestripe->getLieulivraison();
        $html .="</p>
          </columns>
        </row>
      </callout>

      <h4>Détail de la commande</h4>

      <table>
        <tr><th>Produit</th><th>#</th><th style='text-align:right'>Prix</th></tr>";

        foreach ($this->getFullCommande() as $value) {
          $image = $value['food']->getCoverImage();
          $html .= "<tr><td>". $value['food']->getMenu(). "</td><img style='height: 30px; width:35px; display: block;' src='$image' alt='Image' class='img-fluid'><td></td><td>" . number_format($value['food']->getPrice(), 2) ." &euro; </td></tr>";
        }

      $html .= "<tr>
          <td colspan='2'><b>Prix total:</b></td>
          <td>".number_format($commandestripe->getPrixtotal(), 2) ." &euro; </td>
        </tr>
      </table>

      <hr/>

      <h4>Après c'est quoi?</h4>

      <p>Our carrier raven will prepare your order for delivery. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Modi necessitatibus itaque debitis laudantium doloribus quasi nostrum distinctio suscipit, magni soluta eius animi voluptatem qui velit eligendi quam praesentium provident culpa?</p>
    </columns>
  </row>
  <row class='footer text-center'>
    <columns large='3'>
      <img src='http://placehold.it/170x30' alt=''>
    </columns>
    <columns large='3'>
      <p>
        Contactez-nous au 06 00 00 00 00<br/>
        ou par email shangrila@gmail.com
      </p>
    </columns>
    <columns large='3'>
      <p>
        123 rue shangrila<br/>
        75493 Toulouse
      </p>
    </columns>
  </row>
</container>";

      $mail->Body  = $html;
      //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

      
      if($mail->send()){
        foreach ($fullcommande as $value) {
          $id = $value['food']->getId();
          $this->remove($id);
        }
        return ($commande);
      }
      
    } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    
  }
}