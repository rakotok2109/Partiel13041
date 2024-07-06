<?php

namespace App\Form;

use App\Entity\Election;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('theme')
            ->add('quota')
            ->add('numberwinners')
            ->add('propositions', CollectionType::class, [
                'entry_type' => PropositionType::class,
                'allow_add' => true,
                'by_reference' => false,
                'label' => 'Propositions',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Election::class,
        ]);
    }
}
