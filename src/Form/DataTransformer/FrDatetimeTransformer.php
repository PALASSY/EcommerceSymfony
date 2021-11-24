<?php
namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrDatetimeTransformer implements DataTransformerInterface
{
  //Prendre la date initiale(anglo-saxon) et la transformer en date française
  public function transform($dateinitiale)
  {
    //Sécurity: si pas de date, on return vide
    if ($dateinitiale === null) {
      return "";
    }
    //sinon, retourne la date en format français
    return $dateinitiale->format('d/m/Y');
  }

  //Inversement, prendre la date française et transformer en date initiale(anglo-saxon)
  public function reverseTransform($dateFr)
  {
    //Security: si pas de date, on demande à l'utilisateur d'en fournir une
    if ($dateFr === null) {
      throw new TransformationFailedException('Veuillez choisir une date de réservation');
    }
    //Formater la date en française
    $date = \DateTime::createFromFormat('d/m/Y',$dateFr);

    //Security: si cette date formatée n'est pas formatée convenablement on lance une exception
    if ($date === false) {
      // Exception
    }
    //Sinon retourner cette date formatée
    return $date;
    
  }

}


