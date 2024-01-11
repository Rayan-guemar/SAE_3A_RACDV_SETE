<?php

namespace App\Form;

use App\Entity\Festival;
use App\Entity\QuestionBenevole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifierQuestionBenevoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label')
            ->add('typeReponse', ChoiceType::class, [
                'choices'  => [
                    'Text' => 'string',
                    'Email' => 'email',
                    'Numériques' => 'integer',
                    'Date' => 'date',
                    'Oui/Non' => 'boolean',

                ]
            ])
            ->add('modifyQuestion' , SubmitType::class, [
                'label' => 'Modifier la question'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuestionBenevole::class,
        ]);
    }
}
