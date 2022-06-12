<?php

namespace App\Form\PullList;

use App\Entity\Admin;
use App\Entity\PullList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssignPullListLocatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('locator', EntityType::class, [
                'class'         => Admin::class,
                'documentation' => [
                    'type' => 'integer',
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
