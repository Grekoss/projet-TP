<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\GenreRepository;
use App\Repository\DepartmentRepository;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContext;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'label' => 'Pseudo',
            ))
            ->add('lastname', TextType::class, array(
                'label' => 'Nom',
                'attr' => [
                    'class' => 'long'
                ]
            ))
            ->add('firstname', TextType::class, array(
                'label' => 'Prénom',
            ))
            ->add('avatar', FileType::class, array(
                'data_class' => null,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Choisir un avatar'
                ]
            ))
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux champs doivent correspondre.',
                'options' => array(
                    'attr' => [
                        'class' => 'long-password'
                    ]
                ),
                'required' => true,
                'first_options' => array(
                    'label' => 'Mot de Passe'),
                'second_options' => array(
                    'label' => 'Confirmation du mot de passe'),
            ))
            ->add('city', TextType::class, array(
                'label' => 'Ville',
                'attr' => [
                    'class' => 'long'
                ]
            ))
            ->add('birthDate', BirthdayType::class, array(
                'label' => 'Date de naissance',
                'format' => 'dd MMMM yyyy',
                'years' => range(1950, 2001),
                'attr' => [
                    'class' => 'birthDate-form'
                ]
            ))
            ->add('address', TextType::class, array(
                'label' => 'Adresse',
                'label_attr' => [
                    'class' => 'optional',
                ],
                'attr' => [
                    'class' => 'long'
                ]
            ))
            ->add('zipcode', TextType::class, array(
                'label' => 'Code postal',
            ))
            ->add('genre', EntityType::class, array(
                'class' => 'App\Entity\Genre',
                'query_builder' => function (GenreRepository $genre) {
                    return $genre->createQueryBuilder('g')
                                ->orderBy('g.name', 'ASC');
                },
                'label' => 'Genre',
                'label_attr' => [
                    'class' => 'gender'
                ],
                'attr' => [
                    'placeholder' => 'Choisir votre sexe',
                    'class' => 'radio-gender'
                ],
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('email', TextType::class, array(
                'label' => 'Email',
                'attr' => [
                    'class' => 'long'
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
                    'class' => 'long'
                ]
            ))
            ->add('validConditions', CheckboxType::class, array(
                'mapped' => false,
                'label' => 'J\'accepte les thermes et conditions',
                'required' => true,
                'attr' => [
                    'class' => 'agreement'
                ]
            )) 
            ->add('isMailing', CheckboxType::class, array(
                'label' => 'J\'accepte de recevoir des notifications par mail',
            ))
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,null,
            'attr' => [
                'novalidate' => 'novalidate',
                'class' => 'register',
            ],
        ]);
    }
}
