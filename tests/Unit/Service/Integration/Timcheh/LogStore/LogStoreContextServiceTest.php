<?php

namespace App\Tests\Unit\Service\Integration\Timcheh\LogStore;

use App\Service\Integration\Timcheh\LogStore\LogStoreContextService;
use App\Service\Integration\Timcheh\LogStore\LogStoreInterface;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;
use Symfony\Component\Messenger\Envelope;

class LogStoreContextServiceTest extends BaseUnitTestCase
{
    protected LogStoreContextService|null $sut;

    protected Mockery\LegacyMockInterface|LogStoreInterface|Mockery\MockInterface|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = Mockery::mock(LogStoreInterface::class);
        $this->sut      = new LogStoreContextService([$this->resolver]);
    }

    public function testHandle(): void
    {
        $envelop = Mockery::mock(Envelope::class);

        $this->resolver->shouldReceive("support")
                       ->once()
                       ->with($envelop)
                       ->andReturnTrue();

        $this->resolver->shouldReceive("handleLog")
                       ->once()
                       ->with($envelop)
                       ->andReturn();

        $this->sut->handle($envelop);
    }
}
