<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NewTopicType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        

        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'role' => $resolver
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (in_array('ROLE_ADMIN', $options['data']['role'])) {
            $builder
                ->add('name', 
                    TextType::class, [
                    'required' => true,
                    'label' => 'Topic name',
                    'attr' => ['class' => 'form-control']
                ])

                ->add('content', TextareaType::class, [
                    'attr' => [
                        'class' => 'form-control first-message',
                        'rows' => "9", 
                        'cols' => "45"
                    ],
                    'required' => true,
                    'label' => 'First message'
                ])

                ->add('staffOnly', CheckboxType::class, [
                    'label'    => 'Only staff can publish in this topic ?',
                    'required' => false,
                    'attr' => [
                        'class' => 'checkbox'
                    ]
                ])

                ->add('readOnly', CheckboxType::class, [
                    'label'    => 'Read only topic ?',
                    'required' => false,
                    'attr' => [
                        'class' => 'checkbox'
                    ]
                ])

                ->add('Create topic and publish message', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ]
                ])
            ;
        }
        else if(in_array('ROLE_MODO', $options['role'])) {
            $builder
                ->add('name', 
                    TextType::class, [
                    'required' => true,
                    'label' => 'Topic name',
                    'attr' => ['class' => 'form-control']
                ])

                ->add('content', TextareaType::class, [
                    'attr' => [
                        'class' => 'form-control first-message',
                        'rows' => "9", 
                        'cols' => "45"
                    ],
                    'required' => true,
                    'label' => 'First message'
                ])

                ->add('staffOnly', CheckboxType::class, [
                    'label'    => 'Only staff can publish in this topic ?',
                    'attr' => [
                        'class' => 'checkbox'
                    ]
                ])

                ->add('Create topic and publish message', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ]
                ])
            ;
        }
        else {
            $builder
                ->add('name', 
                    TextType::class, [
                    'required' => true,
                    'label' => 'Topic name',
                    //'label' => $options['role'][1],
                    'attr' => ['class' => 'form-control']
                ])

                ->add('content', TextareaType::class, [
                    'attr' => [
                        'class' => 'form-control first-message',
                        'rows' => "9", 
                        'cols' => "45"
                    ],
                    'required' => true,
                    'label' => 'First message'
                ])

                ->add('Create topic and publish message', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ]
                ])
            ;
        }
    }
}
