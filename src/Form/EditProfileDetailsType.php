<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditProfileDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', ChoiceType::class, [
                'choices'  => [
                    'Select your country' => null,
                    'France' => 'France',
                    'England' => 'England',
                    'USA' => 'USA',
                    'System' => 'System',
                    'Elsewhere' => 'Elsewhere'
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Country',
                'required'   => false,
                'empty_data' => NULL
            ])

            ->add('birth_date', DateType::class, [
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
                'label' => 'Birth date (YYYY-MM-DD)',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required'   => false,
                'empty_data' => ''
            ])

            ->add('biography', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => "9", 
                    'cols' => "45",
                    'required' => false
                ],
                'label' => 'Biography',
                'required'   => false,
                'empty_data' => NULL
            ])

            ->add('signature', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => "2", 
                    'cols' => "45"
                ],
                'label' => 'Signature',
                'required'   => false,
                'empty_data' => NULL
            ])

            ->add('Confirm profile details', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
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


