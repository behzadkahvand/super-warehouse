<?php

namespace App\Form;

use App\Dictionary\SellerPackageStatusDictionary;
use App\DTO\GRMarketPlacePackageReceiptData;
use App\Entity\SellerPackage;
use App\Entity\Warehouse;
use Closure;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class GRMarketPlacePackageReceiptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sellerPackage', EntityType::class, [
                'class'         => SellerPackage::class,
                'constraints' => [
                    new Callback($this->checkSellerPackage())

                ],
                'documentation' => [
                    'type' => 'integer',
                ],
            ])
            ->add('warehouse', EntityType::class, [
                'class'         => Warehouse::class,
                'documentation' => [
                    'type' => 'integer',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GRMarketPlacePackageReceiptData::class,
        ]);
    }

    private function checkSellerPackage(): Closure
    {
        return function (SellerPackage $sellerPackage, ExecutionContextInterface $context) {
            if ($sellerPackage->getStatus() !== SellerPackageStatusDictionary::SENT) {
                $context
                    ->buildViolation('Receipt was created for this seller package before!')
                    ->atPath($context->getObject()->getName())
                    ->addViolation();
            }
        };
    }
}
