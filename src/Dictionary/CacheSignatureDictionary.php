<?php

namespace App\Dictionary;

class CacheSignatureDictionary extends Dictionary
{
    public const RELOCATE_PICKING_INVENTORY_COUNT = 'relocate_item_count_locator_%d_storageBin_%d_inventory_%d';

    public static function makeSignature(string $pattern, ...$params): string
    {
        return sprintf($pattern, ...$params);
    }
}
