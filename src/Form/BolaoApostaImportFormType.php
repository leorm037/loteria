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

use App\DTO\BolaoApostaImportDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BolaoApostaImportFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('fileCsv', FileType::class, [
                    'label' => 'label.bolao.aposta.fileCsv',
                    'help' => 'help.bolao.aposta.fileCsv',
                    'required' => true,
                    'mapped' => true,
                    'constraints' => [
                        new File([
                            'maxSize' => '2048k',
                            'mimeTypes' => [
                                'text/csv',
                                'text/plain',
                            ],
                            'mimeTypesMessage' => 'file.csv',
                                ]),
                    ],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BolaoApostaImportDTO::class,
            'translation_domain' => 'label',
            'choice_translation_domain' => 'label',
        ]);
    }
}
