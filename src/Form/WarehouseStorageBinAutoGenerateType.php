<?php

namespace App\Form;

use App\Dictionary\StorageBinAutoGenerationActionTypeDictionary;
use App\Dictionary\StorageBinAutoGenerationStorageLevelDictionary;
use App\DTO\WarehouseStorageBinAutoGenerateData;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageArea;
use Closure;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class WarehouseStorageBinAutoGenerateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startValue', null, [
                'constraints' => [
                    new NotBlank(),
                    new Callback($this->checkTemplateIsValid($options['template'])),
                ],
            ])
            ->add('endValue', null, [
                'constraints' => [
                    new NotBlank(),
                    new Callback($this->checkTemplateIsValid($options['template'])),
                ],
            ])
            ->add('increment', null, [
                'constraints' => [
                    new NotBlank(),
                    new Callback($this->checkIncrementIsValid()),
                ],
            ])
            ->add('actionType', ChoiceType::class, [
                'choices'     => StorageBinAutoGenerationActionTypeDictionary::values(),
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('storageLevel', ChoiceType::class, [
                'choices'     => StorageBinAutoGenerationStorageLevelDictionary::values(),
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('quantityCapacity', null, [
                'constraints' => [
                    new Callback($this->checkNotEmpty()),
                ],
            ])
            ->add('widthCapacity', null, [
                'constraints' => [
                    new Callback($this->checkNotEmpty()),
                ],
            ])
            ->add('heightCapacity', null, [
                'constraints' => [
                    new Callback($this->checkNotEmpty()),
                ],
            ])
            ->add('lengthCapacity', null, [
                'constraints' => [
                    new Callback($this->checkNotEmpty()),
                ],
            ])
            ->add('weightCapacity', null, [
                'constraints' => [
                    new Callback($this->checkNotEmpty()),
                ],
            ])
            ->add('isActiveForStow', CheckboxType::class, [
                'false_values' => [false, 'false', 0, '0', '', null],
            ])
            ->add('isActiveForPick', CheckboxType::class, [
                'false_values' => [false, 'false', 0, '0', '', null],
            ])->add('warehouse', EntityType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'class'       => Warehouse::class,
            ])
            ->add('warehouseStorageArea', EntityType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'class'       => WarehouseStorageArea::class,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WarehouseStorageBinAutoGenerateData::class,
        ]);
        $resolver->setRequired('template');
        $resolver->setAllowedTypes('template', 'string');
    }

    private function checkTemplateIsValid(string $template): Closure
    {
        return function ($inputValue, ExecutionContextInterface $context) use ($template) {
            $form               = $context->getRoot();
            $storageLevel       = $form->get('storageLevel')->getViewData();
            $templateSections   = explode('-', $template);
            $inputValueSections = explode('-', $inputValue);

            for ($i = 0; $i < count($inputValueSections); $i++) {
                if (strlen($inputValueSections[$i]) != strlen($templateSections[$i])) {
                    $context
                        ->buildViolation('The value is not compatible with template')
                        ->atPath($context->getObject()->getName())
                        ->addViolation();

                    return;
                }
            }

            $aisleInputValue = $inputValueSections[0] ?? null;
            $bayInputValue   = $inputValueSections[1] ?? null;
            $cellInputValue  = $inputValueSections[2] ?? null;
            if ($storageLevel === StorageBinAutoGenerationStorageLevelDictionary::AISLE && (is_null($aisleInputValue))) {
                $context
                    ->buildViolation('The value is not compatible with template')
                    ->atPath($context->getObject()->getName())
                    ->addViolation();

                return;
            }

            if ($storageLevel === StorageBinAutoGenerationStorageLevelDictionary::BAY && (is_null($bayInputValue))) {
                $context
                    ->buildViolation('The value is not compatible with template')
                    ->atPath($context->getObject()->getName())
                    ->addViolation();

                return;
            }

            if ($storageLevel === StorageBinAutoGenerationStorageLevelDictionary::CELL && (is_null($cellInputValue))) {
                $context
                    ->buildViolation('The value is not compatible with template')
                    ->atPath($context->getObject()->getName())
                    ->addViolation();

                return;
            }

            for ($i = 0; $i < strlen($inputValue); $i++) {
                if (
                    (ctype_alpha($template[$i]) && !ctype_alpha($inputValue[$i])) ||
                    (is_numeric($template[$i]) && !is_numeric($inputValue[$i]))
                ) {
                    $context
                        ->buildViolation('The value is not compatible with template')
                        ->atPath($context->getObject()->getName())
                        ->addViolation();
                    break;
                }
            }
        };
    }

    private function checkIncrementIsValid(): Closure
    {
        return function ($increment, ExecutionContextInterface $context) {
            $incrementValues = explode('-', $increment);
            foreach ($incrementValues as $incrementValue) {
                if (!is_numeric($incrementValue) || (int) $incrementValue <= 0) {
                    $context
                        ->buildViolation('Increment sections must be numeric and bigger than zero')
                        ->atPath($context->getObject()->getName())
                        ->addViolation();

                    return;
                }
            }
            $form         = $context->getRoot();
            $storageLevel = $form->get('storageLevel')->getViewData();
            if ($storageLevel === StorageBinAutoGenerationStorageLevelDictionary::AISLE && !isset($incrementValues[0])) {
                $context
                    ->buildViolation('Increment section must be set for aisle type')
                    ->atPath($context->getObject()->getName())
                    ->addViolation();
            }
            if ($storageLevel === StorageBinAutoGenerationStorageLevelDictionary::BAY && (!isset($incrementValues[0]) || !isset($incrementValues[1]))) {
                $context
                    ->buildViolation('Increment section must be set for bay type')
                    ->atPath($context->getObject()->getName())
                    ->addViolation();
            }
            if (
                $storageLevel === StorageBinAutoGenerationStorageLevelDictionary::CELL && (
                    !isset($incrementValues[0]) ||
                    !isset($incrementValues[1]) ||
                    !isset($incrementValues[2])
                )
            ) {
                $context
                    ->buildViolation('Increment section must be set for cell type')
                    ->atPath($context->getObject()->getName())
                    ->addViolation();
            }
        };
    }

    private function checkNotEmpty(): Closure
    {
        return function ($field, ExecutionContextInterface $context) {
            $form       = $context->getRoot();
            $actionType = $form->get('actionType')->getViewData();
            if ($actionType === StorageBinAutoGenerationActionTypeDictionary::ADD && empty($field)) {
                $context
                    ->buildViolation('This value should not be blank in ADD action!')
                    ->atPath($context->getObject()->getName())
                    ->addViolation();
            }
        };
    }
}
