<?php

namespace App\Form;

use App\Entity\Creneaux;
use App\Entity\Tache;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lieu', TextType::class, [
                'attr' => [
                    'placeholder' => 'Lieu de la tache...'
                ],
                'required' => true,
                'mapped' => false
            ])
            ->add('heureDebut', TimeType::class, [
                'attr' => [
                    'label' => 'Heure de début de la tache',
                    'placeholder' => 'Heure de début de la tache...'
                ],
                'required' => true,
                'mapped' => false
            ])
            ->add('heureFin', TimeType::class, [
                'attr' => [
                    'label' => 'Heure de fin de la tache',
                    'placeholder' => 'Heure de fin de la tache...'
                ],
                'required' => true,
                'mapped' => false
            ])
            ->add('creerTache', SubmitType::class, [
                'label' => 'Créer la tache'
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
