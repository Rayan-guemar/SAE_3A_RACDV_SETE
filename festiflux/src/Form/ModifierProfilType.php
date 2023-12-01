<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ModifierProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('email', EmailType::class)
            ->add('adresse',TextType::class, [
                'required' => false
            ])
            ->add('description',TextType::class, [
                'required' => false
            ])
            ->add('fichierPhotoProfil',FileType::class,[
                "required"=>false,
                "mapped"=>false,
                "constraints" =>[
                    new File(maxSize : '10M', extensions : ['jpg', 'png'], maxSizeMessage:'Fichier trop lourd',extensionsMessage: 'Format invalide')
                ]
            ])
            ->add('demanderModificationProfil' , SubmitType::class, [
            'label' => 'Modifier'
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
