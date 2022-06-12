<?php

namespace App\Service\StatusTransition;

use Exception;

class ParentItemStateService
{
    public function findLowestStatusItems(
        string $dictionaryClass,
        string $dictionaryConstantKey,
        array $itemsStates,
    ): string {
        $dataDictionary = $dictionaryClass::toArray();

        $sortedStatus              = $dataDictionary[$dictionaryConstantKey] ?? throw new Exception("there is no data for given class in given dictionary!");
        $sortedStatusWithPositions = array_flip($sortedStatus);

        $itemPositions = [];
        foreach ($itemsStates as $itemsStatus) {
            $itemPositions[] = $sortedStatusWithPositions[$itemsStatus] ?? throw new Exception("{$itemsStatus} not exist in given sorted itemsStatus!");
        }

        return $sortedStatus[min($itemPositions)];
    }
}
