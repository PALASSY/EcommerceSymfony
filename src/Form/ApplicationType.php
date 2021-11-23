<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;


/**
 * Page pour le label,placeholder,et autres options dans un tableau
 * 
 * @param string $label
 * @param string $placeholder
 * @param array $options
 * @return array
 */
class ApplicationType extends AbstractType
{
  public function getConfiguration($label,$placeholder,$options=[])
  {
    //Il y aura 2 tableaux à fusionner, on va utiliser array_merge
    return array_merge(['label'=>$label,
                        'attr'=>['placeholder'=>$placeholder]],
                        $options)
                        ;
  }
}