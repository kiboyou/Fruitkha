<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actual_password', PasswordType::class, [
                'label' => 'Votre mot de passe actuel',
                'attr' => [
                    'placeholder' => 'Exemple : Qooeiroiroe@ejfijf67',
                ],
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre mot de passe actuel.',
                    ]),
                    new Length([
                        'min' => 8,
                        'max' => 20,
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe.',
                    ]),
                    new Length([
                        'min' => 8,
                        'max' => 20,
                    ]),
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'required' => true,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Votre nouveau mot de passe',
                    'attr' => [
                    'placeholder' => 'Exemple : Qooeiroiroe@ejfijf67',
                ],
                    'hash_property_path' => 'password',
                ],
                'second_options' => [
                    'label' => 'Confirmez votre nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Exemple : Qooeiroiroe@ejfijf67',
                    ],
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier mon mot de passe',
                'attr' => [
                    'class' => 'btn btn-success',
                ],
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $user = $form->getConfig()->getOptions()['data'];
                $PasswordHasher = $form->getConfig()->getOptions()['PasswordHasher'];

                $actual_password = $form->get('actual_password')->getData();

                $verifie_passrod  = $PasswordHasher->isPasswordValid(
                    $user,
                    $actual_password
                );
                if (!$verifie_passrod) {
                    $form->get('actual_password')->addError(new FormError('Votre mot de passe actuel est incorrect.'));
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'PasswordHasher' => null
        ]);
    }
}
