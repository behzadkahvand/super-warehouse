<?php

namespace App\Form;

use App\Form\DataTransformer\ItemSerialToItemSerialEntityTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemSerialSerialType extends AbstractType
{
    public function __construct(private ItemSerialToItemSerialEntityTransformer $itemSerialEntityTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this->itemSerialEntityTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'invalid_message' => 'The ItemSerial does not exist',
        ]);
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
