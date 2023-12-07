<?php

namespace App\Form;

use App\Entity\Loteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoteriaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('api')
            ->add('slug')
            ->add('nome')
            ->add('dezena')
            ->add('premiar')
            ->add('marcar')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('logo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Loteria::class,
        ]);
    }
}
