<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Table;
use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date',DateType::class,['widget'=>'single_text','label'=>'Date de réservation'])
            ->add('commentaire',TextareaType::class,['label'=>'Commentaire du client'])
            ->add('client',EntityType::class,[
                'class'=>User::class,
                'choice_label'=>function($user){
                    return $user->getFirstname()." ".$user->getLastname();
                },
                'label'=>'Client'
            ])
            ->add('tabledisponible',EntityType::class,[
                'class'=>Table::class,
                'choice_label'=>function($table){
                    if ($table->getNombre() > 1) {
                        return "En ".$table->getPlace()." pour ".$table->getNombre()." personnes";
                    }elseif ($table->getNombre() == 1) {
                        return "En ".$table->getPlace()." pour ".$table->getNombre()." personne";
                    } 
                },
                'label'=>'Lieu et nombre de personne'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'validation_groups'=> ['Default']
        ]);
    }
}
