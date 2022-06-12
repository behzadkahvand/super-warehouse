<?php

namespace App\Form;

use App\Dictionary\StorageBinTypeDictionary;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WarehouseStorageBinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serial', null, [
                'documentation' => ['type' => 'string'],
            ])
            ->add('type', ChoiceType::class, [
                'choices'       => StorageBinTypeDictionary::values(),
                'documentation' => ['type' => 'string', 'enum' => StorageBinTypeDictionary::values()],
            ])
            ->add('quantityCapacity', null, ['documentation' => ['type' => 'integer']])
            ->add('widthCapacity', null, ['documentation' => ['type' => 'integer']])
            ->add('heightCapacity', null, ['documentation' => ['type' => 'integer']])
            ->add('lengthCapacity', null, ['documentation' => ['type' => 'integer']])
            ->add('weightCapacity', null, ['documentation' => ['type' => 'integer']])
            ->add('isActiveForStow', CheckboxType::class, [
                'false_values' => [false, 'false', 0, '0', '', null],
                'documentation' => ['type' => 'boolean']
            ])
            ->add('isActiveForPick', CheckboxType::class, [
                'false_values' => [false, 'false', 0, '0', '', null],
                'documentation' => ['type' => 'boolean']
            ])
            ->add('warehouse', EntityType::class, [
                'class'         => Warehouse::class,
                'documentation' => ['type' => 'integer'],
            ])
            ->add('warehouseStorageArea', EntityType::class, [
                'class'         => WarehouseStorageArea::class,
                'documentation' => ['type' => 'integer'],
            ])
            ->add('parent', EntityType::class, [
                'class'         => WarehouseStorageBin::class,
                'documentation' => ['type' => 'integer'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WarehouseStorageBin::class,
        ]);
    }
}
