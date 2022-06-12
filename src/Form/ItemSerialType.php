<?php

namespace App\Form;

use App\Dictionary\ItemSerialStatusDictionary;
use App\Entity\ItemSerial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemSerialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choices'       => ItemSerialStatusDictionary::values(),
                'documentation' => ['type' => 'string', 'enum' => ItemSerialStatusDictionary::values()],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ItemSerial::class,
        ]);
    }
}
