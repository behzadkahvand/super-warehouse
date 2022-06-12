<?php

namespace App\Form;

use App\DTO\ShipmentPickListData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PickListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, ['documentation' => ['type' => 'integer']])
            ->add('promiseDateFrom', DateType::class, ['widget' => 'single_text', 'documentation' => ['type' => 'string']])
            ->add('promiseDateTo', DateType::class, ['widget' => 'single_text', 'documentation' => ['type' => 'string']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShipmentPickListData::class,
        ]);
    }
}
