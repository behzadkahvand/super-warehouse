<?php

namespace App\Messaging\Middlewares\Event;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\BusNameStamp;

class SetCommandBusNameMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if (str_contains(get_class($envelope->getMessage()), 'App\\Messaging\\Messages\\Command\\')) {
            if (null !== $envelope->last(BusNameStamp::class)) {
                $envelope = $envelope->withoutStampsOfType(BusNameStamp::class);
            }

            $envelope = $envelope->with(new BusNameStamp('command.bus'));
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
