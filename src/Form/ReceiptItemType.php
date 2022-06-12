<?php

namespace App\Form;

use App\DTO\ReceiptItemData;
use App\Entity\Inventory;
use App\Entity\Receipt;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceiptItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', NumberType::class)
            ->add('inventory', EntityType::class, [
                'class' => Inventory::class,
            ])
            ->add('receipt', EntityType::class, [
                'class' => Receipt::class,
            ])
            ->add('receiptType', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReceiptItemData::class,
        ]);
    }
}
