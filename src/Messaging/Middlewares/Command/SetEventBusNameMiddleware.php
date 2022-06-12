<?php

namespace App\Messaging\Middlewares\Command;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\BusNameStamp;

class SetEventBusNameMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if (str_contains(get_class($envelope->getMessage()), 'App\\Messaging\\Messages\\Event\\')) {
            if (null !== $envelope->last(BusNameStamp::class)) {
                $envelope = $envelope->withoutStampsOfType(BusNameStamp::class);
            }

            $envelope = $envelope->with(new BusNameStamp('event.bus'));
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
