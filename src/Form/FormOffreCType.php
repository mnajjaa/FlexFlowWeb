<?php

namespace App\Form;

use App\Entity\Offre;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; 

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class FormOffreCType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => 'Nom de votre offre'
        ])
        ->add('specialite', ChoiceType::class, [ // Utilisation de ChoiceType pour le champ Spécialité
            'label' => 'Spécialité',
            'choices' => [
                'Musculation' => 'Musculation',
                'Cardio' => 'Cardio',
                'Yoga' => 'Yoga',
                'Boxe' => 'Boxe',
            ],
        ])
        ->add('tarif_heure', NumberType::class, [
            'label' => 'Tarif par heure'
        ])
        ->add('etat_offre', TextType::class, [
            'label' => 'État de l\'offre',
            'data' => 'En attente', // Valeur par défaut
            'disabled' => true, // Désactiver le champ pour empêcher les modifications
        ])
        ->add('email', EmailType::class, [
            'label' => 'Email'
        ])
        ->add('coach', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'username', // Changer 'username' par le champ approprié de l'entité User à afficher
            'label' => 'Coach'
        ]);
}

public function configureOptions(OptionsResolver $resolver)
{
    $resolver->setDefaults([
        'data_class' => Offre::class,
    ]);
}
}