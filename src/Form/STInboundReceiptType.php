<?php

namespace App\Form;

use App\DTO\STInboundReceiptFormData;
use App\Entity\Receipt\STOutboundReceipt;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class STInboundReceiptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('outboundReceipt', EntityType::class, [
                'class'         => STOutboundReceipt::class,
                'documentation' => [
                    'type' => 'integer',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => STInboundReceiptFormData::class,
        ]);
    }
}
