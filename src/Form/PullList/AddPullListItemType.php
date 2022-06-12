<?php

namespace App\Form\PullList;

use App\DTO\AddPullListItemData;
use App\Entity\ReceiptItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

class AddPullListItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('receiptItems', EntityType::class, [
                'class'       => ReceiptItem::class,
                'multiple'    => true,
                'constraints' => [
                    new Count([
                        'min'        => 1,
                        'minMessage' => "This value should not be blank."
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddPullListItemData::class,
        ]);
    }
}
