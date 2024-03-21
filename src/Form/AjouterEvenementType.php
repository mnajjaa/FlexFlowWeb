<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\User;
use PhpParser\Node\Stmt\Label;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjouterEvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomEvenement',TextType::class,[
                'label'=>"Nom du l'evenement"
            ])
            ->add('categorie',ChoiceType::class,[
                'label'=>" Catégorie",
                'choices' => [
                    "Fitness"=>"Fitness",
                    "Cycling"=>"Cycling",
                    "Powerlifting"=>" Powerlifting",
                    "Yoga"=>" Yoga",
                     "Gymnastics"=>"  Gymnastics",
                    "Cardio"=>"Cardio",

                ],
                ]
                )

            ->add('Objectif',ChoiceType::class,[
                'label'=>" Objectif",
                'choices' => [
                    "Compétition amicale "=>"Compétition amicale ",
                    "Gain musculaire"=>"Gain musculaire",
                    "Perdre du poid"=>" Perdre du poid",
                    "Renforcement de l'esprit d'équipe "=>" Renforcement de l'esprit d'équipe ",
                  
                ],

            ])
            ->add('nbrPlace',TextType::class,[
                'label'=>"Nombre de place"
            ])
            ->add('Date', DateType::class, [
                'label' => "Date ",
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker'],
            ])
            ->add('Time')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id', // Champ à afficher dans le formulaire
            ])
            ->add('etat', CheckboxType::class, [
                'label' => 'Activer l\'état',
                'required' => false, // Le champ n'est pas obligatoire
            ])
            ->add('image',  FileType::class, [
                'label' => 'Image de l\'événement',
                'mapped' => false, // Indique que ce champ n'est pas associé à une propriété de l'entité
                'required' => false, // Le champ n'est pas requis, il peut être vide
            ])
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
