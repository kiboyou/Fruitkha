<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Fruits;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FruitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom')
        ->add('prix')
        ->add('quantite')
        ->add('illustration', FileType::class, [
            'label' => 'Illustration',
            'mapped' => false,  // Cela signifie que l'image n'est pas directement liée à une entité
            'required' => false,
            'attr' => ['accept' => 'image/*'], // Pour accepter uniquement les images
        ])
         ->add('Categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fruits::class,
        ]);
    }
}
