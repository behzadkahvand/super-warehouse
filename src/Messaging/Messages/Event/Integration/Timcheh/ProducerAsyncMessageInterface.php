<?php

namespace App\Messaging\Messages\Event\Integration\Timcheh;

interface ProducerAsyncMessageInterface
{
    public const SOURCE_SERVICE_NAME = 'SUPER_WAREHOUSE';

    public function getSourceServiceName(): string;
}
