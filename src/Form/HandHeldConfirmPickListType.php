<?php

namespace App\Form;

use App\Dictionary\PickListStatusDictionary;
use App\DTO\HandHeldAcceptPickListData;
use App\Entity\PickList;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class HandHeldConfirmPickListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('items', EntityType::class, [
                'class'       => PickList::class,
                'multiple'    => true,
                'constraints' => [
                    new Callback([
                        'callback' => function (
                            ArrayCollection $pickListItems,
                            ExecutionContextInterface $context,
                            $payload
                        ) {
                            $this->validatePickLists($pickListItems, $context);
                        },
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HandHeldAcceptPickListData::class,
        ]);
    }

    private function validatePickLists(ArrayCollection $pickListItems, ExecutionContextInterface $context): void
    {
        if ($pickListItems->isEmpty()) {
            return;
        }

        /** @var PickList $pickList */
        foreach ($pickListItems as $pickList) {
            if ($pickList->getStatus() !== PickListStatusDictionary::WAITING_FOR_ACCEPT) {
                $context->buildViolation("Only Pick list with WAITING_FOR_ACCEPT status allowed!")
                        ->addViolation();

                return;
            }

            if ($pickList->getPicker() !== null) {
                $context->buildViolation("This pick list already assigned to another picker!")
                        ->addViolation();

                return;
            }
        }
    }
}
