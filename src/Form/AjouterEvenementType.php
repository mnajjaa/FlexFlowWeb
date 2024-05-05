<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\User;
use PhpParser\Node\Stmt\Label;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\File;

class AjouterEvenementType extends AbstractType
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            ->add('nomEvenement',TextType::class,[
                'label'=>"Nom du l'evenement",
                'constraints' => [
                    
                    new Regex([
                        'pattern' => '/^[A-Za-z\s\'\-\.\,\!\?\&\$\%\@\#\*\(\)\[\]\{\}]+$/',
                        'message' => 'Le nom est composé que par des lettres .',
                    ]),
                ],
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => "Catégorie",
                'choices' => [
                    "Fitness" => "Fitness",
                    "Cycling" => "Cycling",
                    "Powerlifting" => "Powerlifting",
                    "Yoga" => "Yoga",
                    "Gymnastics" => "Gymnastics",
                    "Cardio" => "Cardio",
                ],
               
            ])

            ->add('Objectif',ChoiceType::class,[
                'label'=>" Objectif",
                'choices' => [
                    "Compétition amicale "=>"Compétition amicale ",
                    "Gain musculaire"=>"Gain musculaire",
                    "Perdre du poid"=>" Perdre du poid",
                    "Renforcement de l'esprit d'équipe "=>" Renforcement de l'esprit d'équipe ",
                  
                ],
              

            ])
            ->add('nbrPlace', NumberType::class, [
                'label' => "Nombre de place",
                'constraints' => [
                    new Range([
                        'min' => 10,
                        'max' => 40,
                        'notInRangeMessage' => 'Le nombre de place doit être compris entre {{ min }} et {{ max }}.',
                    ]),
                    new Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Le nombre de place doit être un nombre entier.',
                    ]),
                    
                ],
            ])
            ->add('Date', DateType::class, [
                'label' => "Date ",
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker'],
                'constraints' => [
                    new GreaterThan([
                        'value' => 'today', // Date actuelle
                        'message' => 'La date doit être ultérieure à aujourd\'hui.',
                    ]),
                ],
            ])
            ->add('Time', TimeType::class, [ // Ajoutez le champ TimeType
                'label' => 'Heure',
                'widget' => 'single_text', // Utilisez le widget de type texte
                'attr' => ['class' => 'timepicker'], // Ajoutez une classe pour l'initialisation du time picker
               
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'Coach',
                'choice_label' => 'email', // Champ à afficher dans le formulaire
                'choices' => $this->getCoachUsers(),

                'attr' => ['class' => 'form-control'],
               
    
            ])
            ->add('etat', CheckboxType::class, [
                'label' => 'Activer l\'état',
                'required' => false, // Le champ n'est pas obligatoire
            ])
            ->add('imageFile',  FileType::class, [
                'label' => 'Image de l\'événement',
                'mapped' => false, // Indique que ce champ n'est pas associé à une propriété de l'entité
                'required' => false, // Le champ n'est pas requis, il peut être vide
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image au format JPG ou PNG.',
                    ]),
                ],
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
            'data_class' => Evenement::class,
        ]);
    }
}
