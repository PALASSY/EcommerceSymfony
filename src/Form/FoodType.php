<?php

namespace App\Form;

use App\Entity\Food;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class FoodType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('menu', TextType::class, $this->getConfiguration('Menu', 'Le titre du menu'))
            ->add('slug', TextType::class, $this->getConfiguration('Slug', 'Personnalisez un alias pour gérer un URL', ['required' => false]))
            ->add('description', TextareaType::class, $this->getConfiguration('Description', 'Votre description'))
            ->add('coverImage', UrlType::class, $this->getConfiguration('Image de couverture', 'Insérer une image'))
            ->add('price', MoneyType::class, $this->getConfiguration('Prix', 'Mettez le prix du menu', ['invalid_message'=>'Veuillez mettre le prix de votre menu']))
            //->add('category', TextType::class, $this->getConfiguration('Categorie', 'Entree/Plat/dessert'))
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Entrée' => 'Entrée',
                    'Plat' => 'Plat',
                    'Dessert' => 'Dessert',
                ],
                'expanded' => true,
            ])
            ->add('stock', IntegerType::class, $this->getConfiguration('Stock', 'Mettez votre stock'))
            ->add('images', CollectionType::class, ['entry_type' => ImageType::class, 'allow_add' => true, 'allow_delete' => true, 'allow_extra_fields' => 'Test'])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Food::class,
        ]);
    }
}
