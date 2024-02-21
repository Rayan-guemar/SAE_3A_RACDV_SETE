<?php

namespace App\Form;

use App\Entity\Creneaux;
use App\Entity\Poste;
use App\Entity\Tache;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ModifierTacheType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('remarque', TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom de la tâche...'
                ],
                'required' => true,
            ])
            ->add('lieu', TextType::class, [
                'attr' => [
                    'placeholder' => 'Lieu de la tâche...'
                ],
                'required' => true,
                'mapped' => false
            ])
            ->add('nbBenevole', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Nombre de bénévoles...'
                ],
                'required' => true,
                'mapped' => false
              ])
              ->add('poste', TextType::class, [
                'attr' => [
                    'placeholder' => 'Poste relié à la tâche...'
                ],
                'required' => true,
                'mapped' => false
            ])
            ->add('modifTache', SubmitType::class, [
                'label' => 'Modifier la tâche'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
