<?php

namespace App\Form;

use App\Entity\Demande;
use App\Entity\User;
use App\Entity\Offre;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DemandeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('age', IntegerType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('but', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('niveauPhysique', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('maladieChronique', TextType::class ,[
                'constraints' => [
                new NotBlank(),
            ],
        ])
            ->add('nombreHeure', IntegerType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
            ->add('offre', EntityType::class, [
                'class' => Offre::class,
                'choice_label' => 'nom', // Remplacez 'nom' par le champ approprié de l'entité Offre à afficher dans la liste déroulante
            ])
        
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'en_attente',
                    'Acceptée' => 'acceptee',
                    'Refusée' => 'refusee',
                ],
            ])
            ->add('horaire', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('lesjours', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demande::class,
        ]);
    }
}
