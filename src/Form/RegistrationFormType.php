<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
                ->add('nome', TextType::class, [
                    'label' => 'Nome',
                    'required' => true,
                    'attr' => [
                        'autofocus' => true,
                    ],
                ])
                ->add('celular', TelType::class, [
                    'label' => 'Celular',
                    'required' => false,
                ])
                ->add('email', RepeatedType::class, [
                    'type' => EmailType::class,
                    'required' => true,
                    'invalid_message' => 'O e-mail e a confirmação do e-mail estão diferentes.',
                    'first_options' => [
                        'label' => 'E-mail',
                        'attr' => [
                            'autofocus' => true
                        ]
                    ],
                    'second_options' => [
                        'label' => 'Confirme o e-mail'
                    ],
                ])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'required' => true,
                    'invalid_message' => 'A senha e a confirmação da senha estão diferentes.',
                    'first_options' => [
                        'label' => 'Senha',
                        'attr' => [
                            'autocomplete' => 'new-password',
                        ],
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Por favor, informe uma senha.',
                                    ]),
                            new Length([
                                'min' => 6,
                                'minMessage' => 'A senha deve ter no mínimo {{ limit }} caracteres.',
                                'max' => 4096,
                                    ]),
                        ]
                    ],
                    'second_options' => [
                        'label' => 'Confirme a senha',
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Por favor, informe uma senha.',
                                    ]),
                            new Length([
                                'min' => 6,
                                'minMessage' => 'A senha deve ter no mínimo {{ limit }} caracteres.',
                                'max' => 4096,
                                    ]),
                        ]
                    ],
                    'mapped' => false,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
