<?php

namespace App\Form\DataTransformer;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Repository\ItemSerialRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ItemSerialToItemSerialEntityTransformer implements DataTransformerInterface
{
    public function __construct(private ItemSerialRepository $itemSerialRepository)
    {
    }

    /**
     * @param null|ItemSerial $itemSerial
     *
     * @return string
     */
    public function transform($itemSerial): string
    {
        if (null === $itemSerial) {
            return '';
        }

        return $itemSerial->getSerial();
    }

    /**
     * @param string|null $serial
     *
     * @return WarehouseStorageBin|null
     */
    public function reverseTransform($serial): ?ItemSerial
    {
        if (!$serial) {
            return null;
        }

        $itemSerial = $this->itemSerialRepository->findOneBy(['serial' => $serial]);

        if (null === $itemSerial) {
            throw new TransformationFailedException(sprintf(
                'ItemSerial "%s" does not exist!',
                $serial
            ));
        }

        return $itemSerial;
    }
}
