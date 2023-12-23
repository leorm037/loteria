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

use App\DTO\BolaoDTO;
use App\Repository\LoteriaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BolaoFormType extends AbstractType
{
    private LoteriaRepository $loteriaRepository;

    public function __construct(
        LoteriaRepository $loteriaRepository
    ) {
        $this->loteriaRepository = $loteriaRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('loteria', ChoiceType::class, [
                    'label' => 'label.loteria.nome',
                    'choices' => $this->loteriaRepository->list(),
                    'choice_value' => 'id',
                    'choice_label' => 'nome',
                    'placeholder' => 'form.select.placeholder',
                    'required' => true,
                ])
                ->add('concursoNumero', IntegerType::class, [
                    'label' => 'label.concurso.numero',
                ])
                ->add('nome', TextType::class, [
                    'label' => 'label.bolao.nome',
                    'attr' => [
                        'maxlength' => 120,
                    ],
                ])
                ->add('comprovanteAposta', FileType::class, [
                    'label' => 'label.bolao.comprovanteAposta',
                    'help' => 'help.bolao.comprovanteAposta',
                    'required' => false,
                    'mapped' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '10240k',
                            'mimeTypes' => [
                                'application/pdf',
                                'application/x-pdf',
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'help.bolao.comprovanteAposta',
                        ]),
                    ],
                ])
                ->add('valorCota', MoneyType::class, [
                    'label' => 'label.bolao.valorCota',
                    'currency' => 'BRL',
                    'scale' => 2,
                    'required' => false,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BolaoDTO::class,
            'translation_domain' => 'label',
            'choice_translation_domain' => 'label',
        ]);
    }
}
