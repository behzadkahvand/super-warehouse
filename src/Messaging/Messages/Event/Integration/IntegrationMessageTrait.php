<?php

namespace App\Messaging\Messages\Event\Integration;

use App\Messaging\Messages\Event\Integration\Timcheh\ProducerAsyncMessageInterface;

trait IntegrationMessageTrait
{
    public function getSourceServiceName(): string
    {
        return ProducerAsyncMessageInterface::SOURCE_SERVICE_NAME;
    }
}
