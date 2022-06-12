<?php

namespace App\Form;

use App\Entity\SellerPackageItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SellerPackageItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actualQuantity', TextType::class, [
                'documentation' => ['type' => 'integer'],
                'constraints'   => [
                    new PositiveOrZero(),
                    new Callback($this->checkMax()),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SellerPackageItem::class,
        ]);
    }

    private function checkMax()
    {
        return function ($field, ExecutionContextInterface $context) {
            $form             = $context->getRoot();
            $expectedQuantity = $form->getData()->getExpectedQuantity();
            if (!empty($field) && $field > $expectedQuantity) {
                $context
                    ->buildViolation('The value should be smaller than expectedQuantity!')
                    ->atPath($context->getObject()->getName())
                    ->addViolation();
            }
        };
    }
}
