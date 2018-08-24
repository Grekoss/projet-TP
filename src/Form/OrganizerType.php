<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\ParticipantType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Event;

class OrganizerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('participants', CollectionType::class, [
                'entry_type' => ParticipantType::class,
               // 'inherit_data' => true,
               'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
