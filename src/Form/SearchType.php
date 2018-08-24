<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Repository\RegionRepository;
use App\Repository\DepartmentRepository;
use App\Repository\TagRepository;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Repository\GenreRepository;



class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateStart', DateType::class, array(
                'widget' => 'single_text',
                'label' => false,
            ))
            ->add('dateEnd', DateType::class, array(
                'widget' => 'single_text',
                'label' => false,
            ))
            ->add('isRatingOrganizer', CheckboxType::class, array(
                'label' => 'Prendre en compte la note de l\'organisateur',
                'attr' => [
                    'onChange' => 'searchCheck();'
                ]
            ))
            ->add('ratingSelectorOrganizer', RangeType::class, array(
                'label' => 'Choisir la note minimum (0 à 5)',
                'attr' => [
                    'min' => 0,
                    'minMessage' => 'test',
                    'max' => 5,
                ]
            ))
            ->add('choiceLocation', ChoiceType::class, array(
                'label' => false,
                'choices' => [
                    'Pas de restriction de zone' => 'all',
                    'Mon Département' => 'myDepartment',
                    'Ma Région' => 'myRegion',
                    'Choisir un/des département(s)' => 'otherDepartment',
                    'Choisir une/des région(s)' => 'otherRegion',
                ],
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('selectDepartment', EntityType::class, array(
                'label' => false,
                'class' => 'App\Entity\Department',
                'query_builder' => function (DepartmentRepository $de) {
                    return $de->createQueryBuilder('d')
                        ->orderBy('d.number', 'ASC');
                },
                'expanded' => false,
                'multiple' => true,
            ))
            ->add('selectRegion', EntityType::class, array(
                'label' => false,
                'class' => 'App\Entity\Region',
                'query_builder' => function (RegionRepository $region) {
                    return $region->createQueryBuilder('r')
                        ->orderBy('r.name', 'ASC');
                },
                'expanded' => false,
                'multiple' => true,
            ))
            ->add('isAllTags', CheckboxType::class, array(
                'label' => 'Tous les thèmes',
            ))
            ->add('selectTags', EntityType::class, array(
                'label' => false,
                'class' => 'App\Entity\Tag',
                'query_builder' => function (TagRepository $tag) {
                    return $tag->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'd-flex flex-wrap mx-2',
                ]
            ))
            ->add('isFriend', CheckboxType::class, array(
                'label' => 'Amis inscrits'
            ))
            ->add('selectRequete', ChoiceType::class, array(
                'label' => 'Que recherchez-vous ?',
                'choices' => [
                    'Sorties' => 'events',
                    'Membres' => 'members'
                ],
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('isNew', CheckboxType::class, array(
                'label' => 'Nouveaux membres (-3 mois)',
            ))
            ->add('isRatingMember', CheckboxType::class, array(
                'label' => 'Prendre en compte la note des membres',
                'attr' => [
                    'onChange' => 'searchCheck();'
                ]
            ))
            ->add('ratingSelectorMember', RangeType::class, array(
                'label' => 'Choisir la note minimum (0 à 5)',
                'attr' => [
                    'min' => 0,
                    'max' => 5,
                ]
            ))
            ->add('isAge', CheckboxType::class, array(
                'label' => 'Age ?',
                'attr' => [
                    'onChange' => 'searchCheck();'
                ]
            ))
            ->add('ageStart', IntegerType::class, array(
                'label' => false,
                'attr' => [
                    'value' => 18,
                    'min' => 18,
                    'max' => 85,
                ]
            ))
            ->add('ageEnd', IntegerType::class, array(
                'label' => false,
                'attr' => [
                    'value' => 99,
                    'min' => 18,
                    'max' => 85,
                ]
            ))
            ->add('isGenre', CheckboxType::class, array(
                'label' => 'Filtrer par genre',
                'attr' => [
                    'onChange' => 'searchCheck();'
                ]
            ))
            ->add('genre', EntityType::class, array(
                'class' => 'App\Entity\Genre',
                'query_builder' => function (GenreRepository $genre) {
                    return $genre->createQueryBuilder('g')
                        ->orderBy('g.name', 'ASC');
                },
                'label' => false,
                'expanded' => true,
                'multiple' => false,
            ));
    
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'attr' => [
                'novalidate' => 'novalidate',
            ]
        ]);
    }
}