<?php

namespace App\Form;

use App\DTO\NoneReferenceReceiptData;
use App\Entity\Warehouse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoneReferenceReceiptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sourceWarehouse', EntityType::class, [
                'class'         => Warehouse::class,
                'documentation' => [
                    'type' => 'integer',
                ],
            ])
            ->add('destinationWarehouse', EntityType::class, [
                'class'         => Warehouse::class,
                'documentation' => [
                    'type' => 'integer',
                ],
            ])
            ->add('costCenter', TextType::class, [
                'documentation' => [
                    'type' => 'string',
                ],
            ])
            ->add('description', TextType::class, [
                'documentation' => [
                    'type' => 'string',
                ],
            ])
            ->add('type', TextType::class, [
                'documentation' => [
                    'type' => 'string',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NoneReferenceReceiptData::class,
        ]);
    }
}
