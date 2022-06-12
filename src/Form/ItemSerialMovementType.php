<?php

namespace App\Form;

use App\DTO\ItemSerialMovementData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemSerialMovementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('storageBin', BinSerialType::class, [
                'documentation' => [
                    'type' => 'string',
                ],
            ])
            ->add('itemSerial', ItemSerialSerialType::class, [
                'documentation' => [
                    'type' => 'string',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemSerialMovementData::class,
        ]);
    }
}
