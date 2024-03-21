<?php

namespace App\Form;

use App\Entity\Cours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nomCour', TextType::class, [
            'label' => 'Nom du cours'
        ])
        ->add('Duree', TextType::class, [
            'label' => 'Durée'
        ])
        ->add('Intensite', ChoiceType::class, [
            'label' => 'Intensité',
            'choices' => [
                'Faible' => 'Faible',
                'Moyenne' => 'Moyenne',
                'Forte' => 'Forte',
            ],
        ])
            ->add('Cible')
            ->add('Categorie')
            ->add('Objectif')
            ->add('etat')
            ->add('capacite')
            ->add('imageFile', FileType::class, [
                'label' => 'Uploader une image',
                'mapped' => false, // Ce champ ne sera pas mappé à une propriété de l'entité Cours
                'required' => false, // Le champ n'est pas obligatoire
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id', // Champ à afficher dans le formulaire
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}