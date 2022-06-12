<?php

namespace App\Form\DataTransformer;

use App\Entity\WarehouseStorageBin;
use App\Repository\WarehouseStorageBinRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class BinSerialToStorageBinEntityTransformer implements DataTransformerInterface
{
    public function __construct(private WarehouseStorageBinRepository $storageBinRepository)
    {
    }

    /**
     * @param null|WarehouseStorageBin $storageBin
     *
     * @return string
     */
    public function transform($storageBin): string
    {
        if (null === $storageBin) {
            return '';
        }

        return $storageBin->getSerial();
    }

    /**
     * @param string|null $serial
     *
     * @return WarehouseStorageBin|null
     */
    public function reverseTransform($serial): ?WarehouseStorageBin
    {
        if (!$serial) {
            return null;
        }

        $storageBin = $this->storageBinRepository->findOneBy(['serial' => $serial]);

        if (null === $storageBin) {
            throw new TransformationFailedException(sprintf(
                'StorageBin "%s" does not exist!',
                $serial
            ));
        }

        return $storageBin;
    }
}
