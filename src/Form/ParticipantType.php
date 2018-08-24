<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',null, [
                'label' =>  'Nom',
                'attr' => [
                    'readonly'=> true,
                ]
                ])
            ->add('presence', ChoiceType::class, [
                'choices' => [
                    'Présent' => 5,
                    'Absent mais excusé' => 4,
                    'Absent non excusé' => 1,
                ],
                'mapped' => false,
                //'allow_extra_fields '=> true,
            ])
            ->add('comportement', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 5,
                ],
                'label' => 'Veuillez notez le comportement du participant de 0 à 5',
                'mapped' => false,
                //'allow_extra_fields '=> true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
