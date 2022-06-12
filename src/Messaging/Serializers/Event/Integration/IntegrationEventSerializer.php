<?php

namespace App\Messaging\Serializers\Event\Integration;

use App\Messaging\Messages\Event\Integration\AbstractIntegrationMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class IntegrationEventSerializer implements SerializerInterface
{
    public function __construct(private Serializer $serializer, private iterable $integrationMessages)
    {
    }

    public function encode(Envelope $envelope): array
    {
        $data    = $this->serializer->encode($envelope);
        $message = $envelope->getMessage();

        if (!$message instanceof AbstractIntegrationMessage) {
            throw new MessageDecodingFailedException('Invalid message class');
        }

        $data['headers']['type'] = $message->getMessageType();

        return $data;
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        $translatedType                     = $this->translateType($encodedEnvelope['headers']['type']);
        $encodedEnvelope['headers']['type'] = $translatedType;

        return $this->serializer->decode($encodedEnvelope);
    }

    private function translateType(string $type): string
    {
        foreach ($this->integrationMessages as $integrationMessage) {
            if ($integrationMessage->getMessageType() === $type) {
                return get_class($integrationMessage);
            }
        }

        throw new MessageDecodingFailedException('Invalid message type');
    }
}
