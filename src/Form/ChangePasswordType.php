<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('currentPassword', 
                PasswordType::class, [
                'always_empty' => true,
                'required' => true,
                'attr' => ['class' => 'form-control','placeholder' => '••••••••']
            ])

            ->add('newPassword', 
                RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'options' => ['attr' => ['class' => 'form-control','placeholder' => '••••••••']],
                'label_attr' => ['class' => 'newPassword'],
                'first_options'  => ['label' => 'Define your new password'],
                'second_options' => ['label' => 'Confirm it'],
                'help' => ""
            ])

            ->add('Change password', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-danger'
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
