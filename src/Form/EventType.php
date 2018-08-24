<?php

namespace App\Form;

use App\Entity\Event;
use App\Form\TimeLimitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\DepartmentRepository;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;


class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Titre de la sortie',
                'attr' => [
                    'placeholder' => 'Saisir un titre pour votre sortie'
                ]
            ))
            ->add('address', TextType::class, array(
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => 'Adresse'
                ]
            ))
            ->add('zipcode', TextType::class, array(
                'label' => 'Cde Postal',
                'attr' => [
                    'placeholder' => 'Code postal'
                ]
            ))
            ->add('city', TextType::class, array(
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Ville'
                ]
            ))
            ->add('photo', FileType::class, array(
                'data_class' => null,
                'label' => 'Photo',
                'attr' => [
                    'placeholder' => 'Illustration de la sortie'
                ]
            ))
            ->add('price', MoneyType::class, array(
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prix (€)'
                ]
            ))
            ->add('dateAt', DateType::class, array(
                'label' => 'Date de la sortie',
                'widget' => 'single_text',
            ))      
            ->add('timeAt', TimeType::class, array(
                'label' => 'Heure de la sortie',
            ))
            ->add('timeInterval', TimeLimitType::class, [
                'label' => 'Date limite d\'inscription',
                'mapped' => false,
            ])
            
            ->add('description', TextareaType::class, array(
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description'
                ]
            ))
            ->add('participantsLimit',IntegerType::class, array(
                'label' => 'Nombre de places',
                'attr' => [
                    'value' => 2,
                    'min' => 2,
                    'max' => 20,
                ]
                ))
            ->add('department', EntityType::class, array(
                'class' => 'App\Entity\Department',
                'query_builder' => function (DepartmentRepository $de) {
                    return $de->createQueryBuilder('d')
                              ->orderBy('d.number', 'ASC');
                },
                'label' => 'Département',
                'attr' => [
                    'placeholder' => 'Choisir le département de la sortie',
                ]
            )) 
            ->add('tags', EntityType::class, array(
                'label' => 'Choix des Tags',
                'class' => 'App\Entity\Tag',
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'd-flex flex-wrap mx-2',
                ]
            ))
            ->add('visibility',null,[
                'label' =>'Visibilité globale de la sortie',

            ])
            ->add('minAge',null, [
                'label'=> 'Age minimal',
                'attr' => [
                    'min'=> 18,
                    'max' => 85,
                ],
            ])
            ->add('maxAge',null, [
                'label'=> 'Age maximal',
                'attr' => [
                    'min'=> 18,
                    'max' => 85,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,null,
            'attr' => [
                'novalidate' => 'novalidate',
            ]
        ]);
    }
}
