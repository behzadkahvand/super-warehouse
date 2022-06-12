<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('height', NumberType::class, [
                'constraints'   => [new NotBlank()],
                'documentation' => [
                    'type' => 'integer',
                ],
            ])
            ->add('weight', NumberType::class, [
                'constraints'   => [new NotBlank()],
                'documentation' => [
                    'type' => 'integer',
                ],
            ])
            ->add('length', NumberType::class, [
                'constraints'   => [new NotBlank()],
                'documentation' => [
                    'type' => 'integer',
                ],
            ])
            ->add('width', NumberType::class, [
                'constraints'   => [new NotBlank()],
                'documentation' => [
                    'type' => 'integer',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
