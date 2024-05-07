<?php

namespace App\Form;

use App\Entity\Cours;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\File as FileConstraint;



class CoursType extends AbstractType
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }



    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomCour', TextType::class, [
                'label' => 'Nom du cours',
                
            ])
            ->add('Duree', NumberType::class, [
                'label' => 'Durée',
               
    'invalid_message' => 'La durée doit être un nombre.',
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
            
            
            
            
            ->add('capacite',NumberType::class, [
                'label' => 'Capacité',

    'invalid_message' => 'La capacité doit être un nombre.',
                
                ]) 

                ->add('imageFile', FileType::class, [
                    'label' => 'Image',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez télécharger une image.',
                        ]),
                        new FileConstraint([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'Veuillez télécharger une image au format JPEG ou PNG.',
                            'uploadErrorMessage' => 'Une erreur est survenue lors du téléversement du fichier.',
                        ]),
                    ],
                ])

            
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'Coach',
                'choice_label' => 'email',
                'choices' => $this->getCoachUsers(),
                'attr' => ['class' => 'form-control']
            ]);
            
    }

    private function getCoachUsers()
    {
        // Récupérez les utilisateurs ayant le rôle "COACH"
        return $this->userRepository->findByRole('COACH');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}