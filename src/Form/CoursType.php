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
            ->add('Cible', ChoiceType::class, [
                'label' => 'Cible',
                'choices' => [
                    'Enfant' => 'Enfant',
                    'Adulte' => 'Adulte',
                ],
            ])
            ->add('Categorie', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Aquatique' => 'Aquatique',
                    'Cardio' => 'Cardio',
                    'Force' => 'Force',
                    'Danse' => 'Danse',
                    'Kids Island' => 'Kids Island',
                ],
            ])
            ->add('Objectif', ChoiceType::class, [
                'label' => 'Objectif',
                'choices' => [
                    'Perdre du poids' => 'Perdre du poids',
                    'Se défouler' => 'Se défouler',
                    'Se musculer' => 'Se musculer',
                    'S\'entrainer en dansant' => 'S\'entrainer en dansant',
                ],
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Actif' => true,
                    'Inactif' => false,
                ],
                'expanded' => false,
                'multiple' => false,
                'choice_attr' => function($choice, $key, $value) {
                    if ($value) {
                        // Ajouter un espace entre les options "Actif" et "Inactif"
                        return ['style' => 'margin-right: 10px;']; // Ajustez la valeur selon vos besoins
                    } else {
                        return [];
                    }
                },
            ])
            
            
            
            
            ->add('capacite')
            ->add('imageFile', FileType::class, [
                //'label' => 'Uploader une image',
                'mapped' => false, // Ce champ ne sera pas mappé à une propriété de l'entité Cours
                'required' => false, // Le champ n'est pas obligatoire
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Coach',
                'attr' => ['class' => 'form-control'] // Champ à afficher dans le formulaire
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}