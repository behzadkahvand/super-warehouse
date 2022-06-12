<?php

namespace App\Form;

use App\Entity\Admin;
use App\Validator\Mobile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['documentation' => ['type' => 'string']])
            ->add('family', TextType::class, ['documentation' => ['type' => 'string']])
            ->add('isActive', CheckboxType::class, [
                'false_values'  => ['0', 0, false, 'false', '', null],
                'documentation' => ['type' => 'boolean'],
            ])
            ->add('password', TextType::class, [
                'property_path' => 'plainPassword',
                'constraints'   => new NotBlank(),
                'documentation' => ['type' => 'string'],
            ])
            ->add('mobile', null, [
                'constraints'   => [
                    new Mobile(),
                ],
                'documentation' => ['type' => 'string'],
            ])
            ->add('email', null, [
                'constraints'   => [
                    new Email(),
                ],
                'documentation' => ['type' => 'string'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Admin::class,
        ]);
    }
}
