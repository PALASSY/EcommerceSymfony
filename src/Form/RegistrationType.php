<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, $this->getConfiguration('Nom', 'Mettez votre nom'))
            ->add('lastname', TextType::class, $this->getConfiguration('Prénom', 'Mettez votre prénom'))
            ->add('email', EmailType::class, $this->getConfiguration('Email', 'Mettez votre email'))
            ->add('avatar', UrlType::class, $this->getConfiguration('Avatar', 'URL de votre Avatar'))
            ->add('mdphashed', PasswordType::class, $this->getConfiguration('MDP', 'Mettez votre MDP'))
            ->add('passwordConfirm', PasswordType::class, $this->getConfiguration('Confirmation de MDP', 'Veuillez confirmer votre MDP'))
            ->add('introduction', TextType::class, $this->getConfiguration('Introduction', 'Mettez votre introduction'))
            ->add('description', TextareaType::class, $this->getConfiguration('Description', 'Votre description'));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
