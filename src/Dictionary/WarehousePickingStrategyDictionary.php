<?php

namespace App\Dictionary;

class WarehousePickingStrategyDictionary extends Dictionary
{
    public const NONE = 'NONE';
    public const FIFO = 'FIFO';
    public const FEFO = 'FEFO';
    public const LIFO = 'LIFO';
}
