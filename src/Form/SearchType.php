<?php
namespace App\Form;

use App\Entity\Food;
use App\Data\searchData;
use App\Form\ApplicationType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface; 
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class SearchType extends ApplicationType
{


  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('q', TextType::class, $this->getConfiguration(false, 'Rechercher', ['required' => false]))
      //->add('category', EntityType::class, $this->getConfiguration//(false, false, [
      //  'required' => false, 
      //  'class'=>Food::class,
      //  'choice_label' => 'category',
      //  'query_builder' => function (EntityRepository $er) {
      //     return $er->createQueryBuilder('f')
      //               ->orderBy('f.category', 'DESC');
      //  },
      //  'expanded'=> true, 
      //  'multiple' => true
      //  ]))
      ->add('min', NumberType::class, $this->getConfiguration(false, 'min', ['required' => false]))
      ->add('max', NumberType::class, $this->getConfiguration(false, 'max', ['required' => false]))
      ;
  }


  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => searchData::class,
      'method'=> 'GET',
      'csrf_protection' => false
    ]);
  }


  public function getBlockPrefix()
  {
    return '';
  }
}
