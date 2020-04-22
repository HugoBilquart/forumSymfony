<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StaffManagementType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'placeholder' => 'Select user',
                'query_builder' => function (UserRepository $ur) {
                    return $ur->createQueryBuilder('u')
                        ->where('u.roles NOT LIKE :roles')
                        ->setParameter('roles', '%["ROLE_USER", "ROLE_MODO", "ROLE_ADMIN"]%')
                        ->orderBy('u.roles','DESC');
                },
                'choice_label' => 'username',  
                'label' => 'Select user to change his hierarchical position',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])

            ->add('Promote', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])

            ->add('Demote', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-danger'
                ]
            ])
        ;
    }
}
