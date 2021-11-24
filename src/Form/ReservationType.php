<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Table;
use App\Entity\Reservation;
use App\Form\ApplicationType;
use App\Form\DataTransformer\FrDatetimeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReservationType extends ApplicationType
{

    //On va lancer en __construct le DataTransformer 
    private $transformer;

    public function __construct(FrDatetimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', TextType::class, $this->getConfiguration("Date de réservation", "Date de réservation de votre table"))

            ->add('commentaire', TextareaType::class, $this->getConfiguration(false, 'Votre commentaire', ['required' => false]))

            /*->add('client', EntityType::class, [
                'class' => User::class,
                'choice_label' => function ($user) {
                        return $user->getFirstname();
                },
                'expanded' => true,
                'multiple' => true,
                'label' => 'Choisissez votre restaurateur'
            ])

            ->add('tabledisponible', EntityType::class, [
                'class'=> Table::class,
                'choice_label'=> function($table){

                        //dd($table->getId());
                        $tableAuthor = $table->getAuthortable();
                        foreach($tableAuthor->getTables() as $value){

                            return $table->getId()." ".$tableAuthor->getId()." ".$value->getPlace(). " ".$value->getNombre();
                        }
                },
                'expanded'=>true,
                'multiple'=>true,
                'label'=>'Choisissez votre table'
            ])*/;

        //On va récupérer les dates formatées en français et vice-versa
        $builder->get('date')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
