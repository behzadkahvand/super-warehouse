<?php

namespace App\Form\PullList;

use App\Entity\PullList;
use App\Entity\Warehouse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PullListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('warehouse', EntityType::class, [
                'class'         => Warehouse::class,
                'documentation' => [
                    'type' => 'integer',
                ],
            ])
            ->add('priority', TextType::class, [
                'documentation' => [
                    'type' => 'string',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PullList::class,
        ]);
    }
}
