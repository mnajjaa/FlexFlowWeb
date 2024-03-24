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

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => 'nom'
        ])
        ->add('Description', TextType::class, [
            'label' => 'Description'
        ])
        ->add('Prix')

        ->add('Type', ChoiceType::class, [
            'label' => 'Type',
            'choices' => [
                'Accesoires' => 'Accessoires',
                'jeux' => 'jeux',
                'Vitamines' => 'Vitamines',
                'Proteine' => 'Proteine',

                'vetements' => 'vetements',
            ],
        ])
            ->add('quantite')
            ->add('quantite_Vendues')
            ->add('imageFile', FileType::class, [
                'label' => 'Uploader une image',
                'mapped' => false, // Ce champ ne sera pas mappé à une propriété de l'entité Cours
                'required' => false, // Le champ n'est pas obligatoire
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}