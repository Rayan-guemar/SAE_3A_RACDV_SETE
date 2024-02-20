<?php

namespace App\Form;

use App\Entity\Festival;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;



class FestivalType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du festival',
                'attr' => [
                    'placeholder' => 'Nom du festival'
                ]
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début du festival',
                'widget' => 'single_text',
                'data' => new \DateTimeImmutable(),
                'attr' => [
                    'placeholder' => 'Date de début du festival',
                    'min' => (new \DateTimeImmutable())->format('Y-m-d')
                ],
                'input' => 'datetime_immutable',

            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin du festival',
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'Date de fin du festival',
                    'min' => (new \DateTimeImmutable())->format('Y-m-d')
                ],
                'input' => 'datetime_immutable',

            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du festival',
                'attr' => [
                    'placeholder' => 'Description du festival'
                ]
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu du festival',
                'attr' => [
                    'placeholder' => 'Lieu du festival'
                ]
            ])
            ->add('tags', TextType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Tag',
            ])
            ->add('affiche', FileType::class, [
                'attr' => [
                    'placeholder' => 'Affiche du festival'
                ]
            ])
            ->add('lat', HiddenType::class)
            ->add('lon', HiddenType::class)
            ->add('demanderCreation', SubmitType::class, [
                'label' => 'Demander la création'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Festival::class,
        ]);
    }
}
