<?php

namespace App\Form;

use App\Entity\Inventory;
use App\Entity\ItemBatch;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Repository\ReceiptItemRepository;
use Closure;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ItemBatchType extends AbstractType
{
    public function __construct(private ReceiptItemRepository $receiptItemRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', null, [
                'constraints' => [
                    new Positive(),
                    new Callback($this->checkQuantity($options['receiptItem'])),
                ],
            ])
            ->add('expireAt', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('supplierBarcode')
            ->add('consumerPrice')
            ->add('inventory', EntityType::class, [
                'class' => Inventory::class,
            ])
            ->add('receipt', EntityType::class, [
                'class' => Receipt::class,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ItemBatch::class,
        ]);
        $resolver->setRequired('receiptItem');
        $resolver->setAllowedTypes('receiptItem', ReceiptItem::class);
    }

    private function checkQuantity(ReceiptItem $receiptItem): Closure
    {
        return function ($quantity, ExecutionContextInterface $context) use ($receiptItem) {
            $totalReceiptItemBatchQuantities = $this->receiptItemRepository->getTotalReceiptItemBatchQuantities($receiptItem);
            if (($quantity + $totalReceiptItemBatchQuantities) > $receiptItem->getQuantity()) {
                $context
                    ->buildViolation('The value should be smaller than receipt item quantity!')
                    ->atPath($context->getObject()->getName())
                    ->addViolation();
            }
        };
    }
}
