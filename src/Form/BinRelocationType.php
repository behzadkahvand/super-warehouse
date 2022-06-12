<?php

namespace App\Form;

use App\DTO\BinRelocationData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BinRelocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sourceStorageBin', BinSerialType::class, [
                'documentation' => [
                    'type' => 'string',
                ],
            ])
            ->add('destinationStorageBin', BinSerialType::class, [
                'documentation' => [
                    'type' => 'string',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BinRelocationData::class,
        ]);
    }
}
