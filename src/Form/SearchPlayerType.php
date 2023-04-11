<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchPlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', SearchType::class, [
                'required' => false,
                'label' => 'Recherche',
                'attr' => [
                    'placeholder' => 'Pseudo, Tag Discord ou Jeu',
                ],
            ])
            ->add('available', ChoiceType::class, [
                'required' => false,
                'label' => 'Disponibilité',
                'choices' => [
                    'Tous' => null,
                    'Disponible' => true,
                    'Indisponible' => false,
                ],
                'placeholder' => 'Choisissez une option',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
            ]);
    }
}
