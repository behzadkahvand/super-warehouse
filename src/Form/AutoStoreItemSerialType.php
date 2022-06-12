<?php

namespace App\Form;

use App\DTO\AutoStoreItemSerialData;
use App\Entity\ItemBatch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutoStoreItemSerialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('itemBatch', EntityType::class, [
                'class'         => ItemBatch::class,
                'documentation' => ['type' => 'integer'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AutoStoreItemSerialData::class,
        ]);
    }
}
