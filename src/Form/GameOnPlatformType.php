<?php

namespace App\Form;

use App\Entity\GameOnPlatform;
use App\Entity\Platform;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameOnPlatformType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('release_date', DateType::class, [
                'label' => 'Date de sortie',
                'widget' => 'single_text'
            ])
            ->add('platform', EntityType::class, [
                'label' => 'Plate-forme',
                'class' => Platform::class,
                'choice_label' => 'name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GameOnPlatform::class,
        ]);
    }
}
