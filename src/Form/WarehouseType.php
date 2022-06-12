<?php

namespace App\Form;

use App\Dictionary\WarehousePickingStrategyDictionary;
use App\Dictionary\WarehousePickingTypeDictionary;
use App\Dictionary\WarehouseTrackingTypeDictionary;
use App\Entity\Admin;
use App\Entity\Warehouse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WarehouseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'documentation' => ['type' => 'string'],
            ])
            ->add('isActive', CheckboxType::class, [
                'false_values'  => [false, 'false', 0, '0', '', null],
                'documentation' => ['type' => 'boolean'],
            ])
            ->add('trackingType', ChoiceType::class, [
                'choices'       => WarehouseTrackingTypeDictionary::toArray(),
                'documentation' => ['type' => 'string', 'enum' => WarehouseTrackingTypeDictionary::values()],
            ])
            ->add('pickingType', ChoiceType::class, [
                'choices'       => WarehousePickingTypeDictionary::toArray(),
                'documentation' => ['type' => 'string', 'enum' => WarehousePickingTypeDictionary::values()],
            ])
            ->add('pickingStrategy', ChoiceType::class, [
                'choices' => WarehousePickingStrategyDictionary::values(),
                'documentation' => ['type' => 'string', 'enum' => WarehousePickingStrategyDictionary::values()],
            ])
            ->add('forSale', CheckboxType::class, [
                'false_values'  => [false, 'false', 0, '0', '', null],
                'documentation' => ['type' => 'boolean'],
            ])
            ->add('forRetailPurchase', CheckboxType::class, [
                'false_values'  => [false, 'false', 0, '0', '', null],
                'documentation' => ['type' => 'boolean'],
            ])
            ->add('forMarketPlacePurchase', CheckboxType::class, [
                'false_values'  => [false, 'false', 0, '0', '', null],
                'documentation' => ['type' => 'boolean'],
            ])
            ->add('forFmcgMarketPlacePurchase', CheckboxType::class, [
                'false_values'  => [false, 'false', 0, '0', '', null],
                'documentation' => ['type' => 'boolean'],
            ])
            ->add('forSalesReturn', CheckboxType::class, [
                'false_values'  => [false, 'false', 0, '0', '', null],
                'documentation' => ['type' => 'boolean'],
            ])
            ->add('coordinates', PointType::class)
            ->add('address', null, [
                'documentation' => ['type' => 'string'],
            ])
            ->add('owner', EntityType::class, [
                'class'         => Admin::class,
                'documentation' => ['type' => 'integer'],
            ])
            ->add('phone', null, [
                'documentation' => ['type' => 'string'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Warehouse::class,
        ]);
    }
}
