<?php

namespace App\Dictionary;

class ReceiptStatusDictionary extends Dictionary
{
    public const DRAFT = 'DRAFT';
    public const RESERVED = 'RESERVED';
    public const WAITING_FOR_SUPPLY = 'WAITING_FOR_SUPPLY';
    public const APPROVED = 'APPROVED';
    public const BATCH_PROCESSING = 'BATCH_PROCESSING';
    public const LABEL_PRINTING = 'LABEL_PRINTING';
    public const READY_TO_STOW = 'READY_TO_STOW';
    public const STOWING = 'STOWING';
    public const READY_TO_PICK = 'READY_TO_PICK';
    public const PICKING = 'PICKING';
    public const CANCELED = 'CANCELED';
    public const DONE = 'DONE';
}
