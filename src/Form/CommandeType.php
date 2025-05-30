<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Facture;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_commande', null, [
                'widget' => 'single_text',
            ])
            ->add('status')
            ->add('addresse_livraison')
            ->add('mode_paiement')
            ->add('client', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstName',
            ])
            ->add('facture', EntityType::class, [
                'class' => Facture::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
