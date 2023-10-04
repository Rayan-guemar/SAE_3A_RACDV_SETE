<?php

namespace App\Form;

use App\Entity\DemandeFestival;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandeFestivalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomFestival')
            ->add('dateDebutFestival')
            ->add('dateFinFestival')
            ->add('descriptionFestival')
            ->add('lieuFestival')
            ->add('afficheFestival')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DemandeFestival::class,
        ]);
    }
}
