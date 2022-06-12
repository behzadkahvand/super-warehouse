<?php

namespace App\Form;

use App\Entity\WarehouseOwner;
use App\Validator\Mobile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

class WarehouseOwnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'documentation' => ['type' => 'string']
            ])
            ->add('family', null, [
                'documentation' => ['type' => 'string']
            ])
            ->add('mobile', null, [
                'constraints' => [
                    new Mobile(),
                ],
                'documentation' => ['type' => 'string', 'pattern' => '/^((09)[\d]{9})+$/']
            ])
            ->add('email', null, [
                'constraints' => [
                    new Email(),
                ],
                'documentation' => ['type' => 'string', 'format' => 'email']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WarehouseOwner::class,
        ]);
    }
}
