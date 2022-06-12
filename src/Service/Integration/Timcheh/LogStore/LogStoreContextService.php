<?php

namespace App\Service\Integration\Timcheh\LogStore;

use Symfony\Component\Messenger\Envelope;

class LogStoreContextService
{
    public function __construct(private iterable $resolvers)
    {
    }

    public function handle(Envelope $envelope): void
    {
        /** @var LogStoreInterface $resolver */
        foreach ($this->resolvers as $resolver) {
            if ($resolver->support($envelope)) {
                $resolver->handleLog($envelope);
            }
        }
    }
}
