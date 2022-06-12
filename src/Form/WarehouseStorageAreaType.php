<?php

namespace App\Form;

use App\Dictionary\StorageAreaCapacityCheckMethodDictionary;
use App\Dictionary\StorageAreaStowingStrategyDictionary;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageArea;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class WarehouseStorageAreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'constraints' => new Length(['max' => 255]),
                'documentation' => ['type' => 'string'],
            ])
            ->add('stowingStrategy', ChoiceType::class, [
                'choices' => StorageAreaStowingStrategyDictionary::values(),
                'documentation' => ['type' => 'string', 'enum' => StorageAreaStowingStrategyDictionary::values()],
            ])
            ->add('capacityCheckMethod', ChoiceType::class, [
                'choices' => StorageAreaCapacityCheckMethodDictionary::values(),
                'documentation' => ['type' => 'string', 'enum' => StorageAreaCapacityCheckMethodDictionary::values()],
            ])
            ->add('isActive', CheckboxType::class, [
                'documentation' => ['type' => 'boolean'],
            ])
            ->add('warehouse', EntityType::class, [
                'class' => Warehouse::class,
                'documentation' => ['type' => 'integer'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WarehouseStorageArea::class,
        ]);
    }
}
