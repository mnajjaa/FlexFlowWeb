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
use Symfony\Component\Validator\Constraints as Assert;

class FormOffreCType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de votre offre',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('specialite', ChoiceType::class, [
                'label' => 'Spécialité',
                'choices' => [
                    'Musculation' => 'Musculation',
                    'Cardio' => 'Cardio',
                    'Yoga' => 'Yoga',
                    'Boxe' => 'Boxe',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('tarif_heure', NumberType::class, [
                'label' => 'Tarif par heure',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'float', 'message' => 'Veuillez saisir un nombre décimal.']),
                    new Assert\GreaterThan(['value' => 0, 'message' => 'Le tarif par heure doit être supérieur à zéro.']),
                ],
            ])
            ->add('etat_offre', TextType::class, [
                'label' => 'État de l\'offre',
                'data' => 'En attente', // Valeur par défaut
                'disabled' => true, // Désactiver le champ pour empêcher les modifications
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
            ])
            ->add('coach', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username', // Changer 'username' par le champ approprié de l'entité User à afficher
                'label' => 'Coach',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
        ]);
    }
}