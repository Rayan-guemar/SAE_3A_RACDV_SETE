<?php

namespace App\Form;

use App\Entity\DemandeFestival;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandeFestivalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomFestival' , TextType::class, [
                'label' => 'Nom du festival',
                'attr' => [
                    'placeholder' => 'Nom du festival'
                ]
            ])
            ->add('dateDebutFestival' , DateType::class, [
                'label' => 'Date de début du festival',
                'attr' => [
                    'placeholder' => 'Date de début du festival'
                ],
                'input' => 'datetime_immutable',
            ])
            ->add('dateFinFestival' , DateType::class, [
                'label' => 'Date de fin du festival',
                'attr' => [
                    'placeholder' => 'Date de fin du festival'
                ],
                'input' => 'datetime_immutable',
            ])
            ->add('descriptionFestival' , TextareaType::class, [
                'label' => 'Description du festival',
                'attr' => [
                    'placeholder' => 'Description du festival'
                ]
            ])
            ->add('lieuFestival' , TextType::class, [
                'label' => 'Lieu du festival',
                'attr' => [
                    'placeholder' => 'Lieu du festival'
                ]
            ])
            ->add('afficheFestival' , TextType::class, [
                'label' => 'Lien de l\'affiche du festival',
                'attr' => [
                    'placeholder' => 'Affiche du festival'
                ]
            ])
            ->add('demanderCreationFestival' , SubmitType::class, [
                'label' => 'Demander la création'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DemandeFestival::class,
        ]);
    }
}
