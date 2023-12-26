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

use App\DTO\BolaoApostaDTO;
use App\Repository\LoteriaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Uid\Uuid;

class BolaoApostaFormType extends AbstractType
{
    private LoteriaRepository $loteriaRepository;

    public function __construct(LoteriaRepository $loteriaRepository)
    {
        $this->loteriaRepository = $loteriaRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('idLoteria', HiddenType::class, [
                ])
                ->add('dezenasMarcar', ChoiceType::class, [
                    'choices' => null,
                    'label' => 'label.aposta.dezena.marcar',
                    'placeholder' => 'form.select.placeholder',
                    'help' => 'help.aposta.dezena.marcar',
                    'required' => true,
                    'attr' => [
                        'autofocus' => true,
                    ],
                ])
                ->add('dezenas', CollectionType::class, [
                    'entry_type' => IntegerType::class,
                    'required' => true,
                    'entry_options' => [
                        'label' => false,
                        'required' => true,
                        'row_attr' => [
                            'class' => 'col-3 col-md-2 mb-3',
                        ],
                    ],
                    'label' => 'Dezenas',
                    'attr' => [
                        'class' => 'row',
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                ])
                ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit'])
                ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData'])
        ;
    }

    public function onPreSubmit(FormEvent $event): void
    {
        /** @var BolaoApostaDTO $bolaoApostaDTO */
        $bolaoApostaDTO = $event->getData();
        $form = $event->getForm();

        $uuidLoteria = Uuid::fromString($bolaoApostaDTO['idLoteria']);
        $loteria = $this->loteriaRepository->findById($uuidLoteria);

        $dezenasQuantidade = array_combine(
            $loteria->getMarcar(),
            $loteria->getMarcar()
        );

        $form
                ->add('dezenasMarcar', ChoiceType::class, [
                    'choices' => $dezenasQuantidade,
                    'label' => 'label.aposta.dezena.marcar',
                    'placeholder' => 'help.aposta.dezena.marcar',
                    'required' => true,
                    'attr' => [
                        'autofocus' => true,
                    ],
                ])
                ->add('dezenas', CollectionType::class, [
                    'entry_type' => IntegerType::class,
                    'required' => true,
                    'entry_options' => [
                        'label' => false,
                        'required' => true,
                        'row_attr' => [
                            'class' => 'col-3 col-md-2 mb-3',
                        ],
                        'attr' => [
                            'min' => min($loteria->getDezena()),
                            'max' => max($loteria->getDezena()),
                        ],
                    ],
                    'label' => 'Dezenas',
                    'attr' => [
                        'class' => 'row',
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                ])
        ;
    }

    public function onPreSetData(FormEvent $event): void
    {
        /** @var BolaoApostaDTO $bolaoApostaDTO */
        $bolaoApostaDTO = $event->getData();
        $form = $event->getForm();

        if (null === $bolaoApostaDTO && !($bolaoApostaDTO instanceof BolaoApostaDTO)) {
            return;
        }

        $uuidLoteria = Uuid::fromString($bolaoApostaDTO->getIdLoteria());
        $loteria = $this->loteriaRepository->findById($uuidLoteria);

        $dezenasQuantidade = array_combine(
            $loteria->getMarcar(),
            $loteria->getMarcar()
        );

        $form
                ->add('dezenasMarcar', ChoiceType::class, [
                    'choices' => $dezenasQuantidade,
                    'label' => 'label.aposta.dezena.marcar',
                    'placeholder' => 'form.select.placeholder',
                    'help' => 'help.aposta.dezena.marcar',
                    'required' => true,
                    'attr' => [
                        'autofocus' => true,
                    ],
                ])
                ->add('dezenas', CollectionType::class, [
                    'entry_type' => IntegerType::class,
                    'required' => true,
                    'entry_options' => [
                        'label' => false,
                        'required' => true,
                        'row_attr' => [
                            'class' => 'col-3 col-md-2 mb-3',
                        ],
                        'attr' => [
                            'min' => min($loteria->getDezena()),
                            'max' => max($loteria->getDezena()),
                        ],
                    ],
                    'label' => 'Dezenas',
                    'attr' => [
                        'class' => 'row',
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BolaoApostaDTO::class,
            'translation_domain' => 'label',
            'choice_translation_domain' => 'label',
        ]);
    }
}
