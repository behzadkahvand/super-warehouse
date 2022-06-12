<?php

namespace App\Tests\Unit\Messaging\Serializers\Command\Async;

use App\Messaging\Messages\Command\Async\AsyncMessage;
use App\Messaging\Messages\Command\ItemSerial\AddSerialToItemSerial;
use App\Messaging\Serializers\Command\Async\AsyncMessageSerializer;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;

class AsyncMessageSerializerTest extends BaseUnitTestCase
{
    protected PhpSerializer|LegacyMockInterface|MockInterface|null $serializerMock;

    protected ?AsyncMessageSerializer $messageSerializer;

    protected ?array $encodedEnvelope;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializerMock    = Mockery::mock(PhpSerializer::class);
        $this->messageSerializer = new AsyncMessageSerializer($this->serializerMock);
        $this->encodedEnvelope   = ['body' => 'message body'];
    }

    public function testItCanDecodeEncodedEnvelope(): void
    {
        $this->serializerMock->shouldReceive('decode')
                             ->once()
                             ->with($this->encodedEnvelope)
                             ->andReturn(new Envelope(new stdClass()));

        $this->messageSerializer->decode($this->encodedEnvelope);
    }

    public function testItCanEncodeEnvelopeWhenEnvelopeMessageIsNotAsyncMessage(): void
    {
        $envelope = new Envelope(new stdClass());

        $this->serializerMock->shouldReceive('encode')
                             ->once()
                             ->with($envelope)
                             ->andReturn($this->encodedEnvelope);

        $this->messageSerializer->encode($envelope);
    }

    public function testItCanEncodeEnvelopeWhenEnvelopeMessageIsAsyncMessage(): void
    {
        $asyncMessage = new AsyncMessage(new AddSerialToItemSerial(12));
        $envelope     = new Envelope($asyncMessage);

        $this->serializerMock->shouldReceive('encode')
                             ->once()
                             ->with(Mockery::type(Envelope::class))
                             ->andReturn($this->encodedEnvelope);

        $this->messageSerializer->encode($envelope);
    }
}
