<?php

namespace App\Form;

use App\Entity\Produit;
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
use Symfony\Component\Validator\Constraints as Assert;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => 'Nom du produit',
            
        ])
    ->add('description', TextType::class, [
        'label' => 'description',
       
    ])

        ->add('prix', NumberType::class, [
            'label' => 'prix',
            'invalid_message' => 'Veuillez saisir un prix valide (chiffres uniquement et supérieur à zéro)',
            'attr' => ['min' => 0],
        ])

        ->add('Type', ChoiceType::class, [
            'label' => 'Type',
            'choices' => [
                'Accesoires' => 'Accessoires',
                'jeux' => 'jeux',
                'Vitamines' => 'Vitamines',
                'Proteine' => 'Proteine',
                'vetements' => 'vetements',
                'Fruits' => 'Fruits',

            ],
        ])
        ->add('quantite', NumberType::class, [
            'label' => 'Quantité',
            'invalid_message' => 'Veuillez saisir une quantité valide (chiffres uniquement et supérieur à zéro)',
            'attr' => ['min' => 0],
        ])
        ->add('quantiteVendues', NumberType::class, [
            'label' => 'Quantité vendues',
            'invalid_message' => 'Veuillez saisir une quantité vendue valide (chiffres uniquement et supérieur à zéro)',
            'attr' => ['min' => 0],
        ])
        ->add('imageFile', FileType::class, [
            'label' => 'Uploader une image',
            'mapped' => false,
            'required' => true,
            'constraints' => [
                new NotBlank(['message' => 'Veuillez télécharger une image.']),
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }













    
}  