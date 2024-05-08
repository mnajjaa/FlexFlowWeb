<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtatFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    
                    'Non_traite' => 'Non_traite',
                    'Traite' => 'Traite',
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('titre_reclamation', null, ['disabled' => true])
            ->add('description', null, ['disabled' => true])
            ->add('user', null, ['disabled' => true]); // Ajout de l'attribut user, désactivé
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
