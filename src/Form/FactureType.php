<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Facture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant_total')
            ->add('date_emission', null, [
                'widget' => 'single_text',
            ])
            ->add('status')
            ->add('commande', EntityType::class, [
                'class' => Commande::class,
                'choice_label' => 'client.firstname',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
