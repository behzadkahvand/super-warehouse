<?php

namespace App\Form;

use App\DTO\CustomStoreItemSerialData;
use App\Entity\ItemBatch;
use App\Service\ItemSerial\ItemSerialService;
use Closure;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CustomStoreItemSerialType extends AbstractType
{
    public function __construct(private ItemSerialService $itemSerialService)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('itemBatch', EntityType::class, [
                'class'         => ItemBatch::class,
                'documentation' => ['type' => 'integer'],
            ])
            ->add(
                'serials',
                CollectionType::class,
                [
                    'prototype'     => true,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,
                    'documentation' => ['type' => 'array', 'items' => 'string'],
                    'constraints'   => [
                        new Callback($this->checkItemSerialCount()),
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomStoreItemSerialData::class,
        ]);
    }

    private function checkItemSerialCount(): Closure
    {
        return function (array $serials, ExecutionContextInterface $context) {
            $form = $context->getRoot();
            /** @var ItemBatch $itemBatch */
            $itemBatch = $form->get('itemBatch')->getData();
            if (count($serials) > $this->itemSerialService->getRemainedItemSerialsCount($itemBatch)) {
                $context
                    ->buildViolation('Serials count is bigger than remained item serials!')
                    ->atPath($context->getObject()->getName())
                    ->addViolation();
            }
        };
    }
}
