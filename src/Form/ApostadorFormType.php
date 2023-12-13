<?php

/*
 *     This file is part of Loteria.
 *
 *     (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 *     This source file is subject to the MIT license that is bundled
 *     with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Apostador;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ApostadorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('nome', TextType::class, [
                    'label' => 'label.bolao.apostador.nome',
                    'required' => true,
                    'attr' => [
                        'maxlength' => 60,
                    ],
                ])
                ->add('email', EmailType::class, [
                    'label' => 'label.bolao.apostador.email',
                    'required' => false,
                ])
                ->add('celular', TelType::class, [
                    'label' => 'label.bolao.apostador.celular',
                    'required' => false,
                ])
                ->add('isPago', ChoiceType::class, [
                    'label' => 'label.bolao.apostador.isPago',
                    'choices' => [
                        'form.select.option.sim' => true,
                        'form.select.option.nao' => false,
                    ],
                    'placeholder' => 'form.select.placeholder',
                    'required' => true,
                ])
                ->add('quantidadeCota', IntegerType::class, [
                    'label' => 'label.bolao.apostador.quantidadeCota',
                    'required' => true,
                ])
                ->add('comprovante', FileType::class, [
                    'label' => 'label.bolao.apostador.comprovante',
                    'help' => 'help.bolao.apostador.comprovante',
                    'required' => false,
                    'mapped' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '2048k',
                            'mimeTypes' => [
                                'application/pdf',
                                'application/x-pdf',
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'help.bolao.apostador.comprovante',
                                ]),
                    ],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Apostador::class,
            'translation_domain' => 'label',
            'choice_translation_domain' => 'label',
        ]);
    }
}
