<?php

namespace App\Service\Integration\Timcheh\LogStore;

use Symfony\Component\Messenger\Envelope;

interface LogStoreInterface
{
    public function support(Envelope $envelope): bool;

    public function handleLog(Envelope $envelope): void;

    public static function getPriority(): int;
}
