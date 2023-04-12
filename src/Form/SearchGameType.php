<?php

namespace App\Form;

use App\Entity\Platform;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', SearchType::class, [
                'required' => false,
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Rechercher par titre',
                ],
            ])
            ->add('platform', ChoiceType::class, [
                'required' => false,
                'label' => 'Plateforme',
                'placeholder' => 'Choisissez une plateforme',
                'choices' => array_reduce($options['platforms'], function ($result, $platform) {
                    $result[$platform->getName()] = $platform->getName();
                    return $result;
                }, []),
                'choice_label' => function ($value, $key, $index) {
                    return $value;
                }
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
            ]);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('platforms');
        $resolver->setAllowedTypes('platforms', 'array');
    }
}
