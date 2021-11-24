<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, $this->getConfiguration("MDP actuel", "Saisissez votre MDP actuel"))
            ->add('newPassword', PasswordType::class, $this->getConfiguration("Nouveau MDP", "Saisissez un nouveau MPD"))
            ->add("confirmPassword", PasswordType::class, $this->getConfiguration("Confirmation de MDP", "Confirmez votre MDP"));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
