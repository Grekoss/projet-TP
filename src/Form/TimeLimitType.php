<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class TimeLimitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('interval',ChoiceType::class,[
                'label' => false,
                'choices' => [
                    'heure de la sortie'=> '-1 second',
                    '1 heure avant' => '-1 hour',
                    '2 heures avant' => '-2 hour',
                    '3 heures avant' => '-3 hour',
                    '4 heures avant' => '-4 hour',
                    '6 heures avant' => '-6 hour',
                    '8 heures avant' => '-8 hour',
                    '12 heures avant' => '-12 hour',
                    '1 jour avant' => '-1 day',
                    '2 jours avant' => '-2 day',
                    '3 jours avant' => '-3 day',
                    '4 jours avant' => '-4 day',
                    '7 jours avant' => '-7 day',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
