<?php

namespace App\Form;

use App\Entity\Bulletin;
use App\Entity\Election;
use App\Entity\Proposition;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class BulletinType extends AbstractType
{
    /* L'utilisateur peut choisir les propositions qu'il veut pour son bulletin, 
    il peut cocher une proposition minimum et tous maximum*/
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('choice', EntityType::class, [
            'class' => Proposition::class,
            'choice_label' => 'name',
            'expanded' => true,
            'multiple' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bulletin::class,
        ]);
    }

}
