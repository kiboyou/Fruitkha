<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email',
                'attr' => [
                    'placeholder' => 'Exemple : nom@gmail.com',
                ],
                'required' => true,
                'invalid_message' => 'Veuillez saisir une adresse email valide.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une adresse email.',
                    ]),
                ],
            ])
//            ->add('roles')
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
                    'label' => 'Votre mot de passe',
                    'attr' => [
                    'placeholder' => 'Exemple : Qooeiroiroe@ejfijf67',
                ],
                    'hash_property_path' => 'password',
                ],
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe',
                    'attr' => [
                        'placeholder' => 'Exemple : Qooeiroiroe@ejfijf67',
                    ],
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Vos prénoms',
                'attr' => [
                    'placeholder' => 'Exemple : Kiboyou Mohamed',
                ],
                'required' => true,
                'invalid_message' => 'Veuillez saisir votre prénom.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre prénom.',
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
                'attr' => [
                    'placeholder' => 'Exemple : OUATTARA',
                ],
                'required' => true,
                'invalid_message' => 'Veuillez saisir votre nom.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nom.',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire',
                'attr' => [
                    'class' => 'btn btn-success',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => 'email',
                    'message' => 'Cette adresse email est déjà utilisée.',
                ]),
            ],
            'data_class' => User::class,
        ]);
    }
}
