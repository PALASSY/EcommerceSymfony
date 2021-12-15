<?php

namespace App\Form;

use App\Entity\Adress;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdressType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pays', TextType::class,$this->getConfiguration('Pays','Mettez le pays de votre adresse'))
            ->add('adressepostale',TextType::class,$this->getConfiguration('Adresse Postale','Mettez votre adress'))
            ->add('complementadresse',TextType::class,$this->getConfiguration('Complement adresse','Mettez le complément de votre adresse'))
            ->add('ville', TextType::class, $this->getConfiguration('Ville','Ville de votre adresse'))
            ->add('region',TextType::class,$this->getConfiguration('Région','Région de votre adresse'))
            ->add('codepostale',IntegerType::class,$this->getConfiguration('Code Postal','Votre code postale'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adress::class,
        ]);
    }
}
