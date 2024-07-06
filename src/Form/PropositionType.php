<?php

namespace App\Form;

use App\Entity\Bulletin;
use App\Entity\Election;
use App\Entity\Proposition;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('election', EntityType::class, [
                'class' => Election::class,
                'choice_label' => 'id',
            ])
            ->add('bulletins', EntityType::class, [
                'class' => Bulletin::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Proposition::class,
        ]);
    }
}
