<?php
namespace App\Form;
use App\Entity\Demande;
use App\Entity\User;
use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

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
         
            ->add('age', NumberType::class, [
                'label' => 'age',
                'invalid_message' => 'Veuillez saisir un age valide (chiffres uniquement et supérieur à zéro)',
                'attr' => ['min' => 0],
            ])
            ->add('but', TextType::class, [
                'label' => 'But',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(?=.*[a-zA-Z\s\'\-\.\,\!\?\&\$\%\@\#\*\(\)\[\]\{\}àéèç])+[a-zA-Z0-9\s\'\-\.\,\!\?\&\$\%\@\#\*\(\)\[\]\{\}àéèç]*$/u',
                        'message' => 'Votre but doit contenir au moins une lettre.',
                    ]),
                ],
            ])
            ->add('niveauPhysique', ChoiceType::class, [
                'choices' => [
                    'Débutant '=> 'Débutant ',
                    'Intermédiaire'=> 'Intermédiaire',
                    'Avancé'=>'Avancé'],

                    'placeholder' => 'Sélectionnez votre niveau',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('maladieChronique', ChoiceType::class, [
                'choices' => [
                    'Je suis en bonne santé'=>'Je suis en bonne santé',
                    'Maladies infectieuses contagieuses' => 'Maladies infectieuses contagieuses',
                    'Maladies oculaires graves' => 'Maladies oculaires graves',
                    'Troubles musculo-squelettiques graves' => 'Troubles musculo-squelettiques graves',
                    'Problèmes neurologiques' => 'Problèmes neurologiques',
                    'Hypertension artérielle non contrôlée' => 'Hypertension artérielle non contrôlée',
                    'Problèmes cardiaques graves' => 'Problèmes cardiaques graves',
                    'Maladies cardiaques graves'=> 'Maladies cardiaques graves',
                    'Hypertension artérielle non contrôlée'=> 'Hypertension artérielle non contrôlée',
                    'Problèmes respiratoires sévères'=> 'Problèmes respiratoires sévères',
                    'Maladies vasculaires périphériques'=> 'Maladies vasculaires périphériques',
                    'Diabète non contrôlé'=> 'Diabète non contrôlé',
                    'Infections actives'=> 'Infections actives',


                ],
                'placeholder' => 'Sélectionnez une maladie chronique',
                'constraints' => [            new NotBlank(),
            ],
        ])
       
        ->add('nombreHeure', NumberType::class, [
            'label' => 'nombreHeure',
            'invalid_message' => 'Veuillez saisir un nombre des heures valide (chiffres uniquement et supérieur à zéro)',
            'attr' => ['min' => 0],
        ])
        ->add('user', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'username',
        ])
        ->add('offre', EntityType::class, [
            'class' => Offre::class,  
               'choice_label' => 'nom',
            ])

            ->add('etat', TextType::class, [
                'label' => 'État de la demande',
                'data' => 'En attente', // Valeur par défaut
                'disabled' => true, // Désactiver le champ pour empêcher les modifications
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