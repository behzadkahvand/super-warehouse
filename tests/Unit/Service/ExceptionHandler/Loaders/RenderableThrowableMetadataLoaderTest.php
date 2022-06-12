<?php

namespace App\Tests\Unit\Service\ExceptionHandler\Loaders;

use App\Service\ExceptionHandler\Loaders\RenderableThrowableMetadataLoader;
use App\Service\ExceptionHandler\RenderableThrowableInterface;
use App\Service\ExceptionHandler\ThrowableMetadata;
use Exception;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RenderableThrowableMetadataLoaderTest extends MockeryTestCase
{
    protected TranslatorInterface|LegacyMockInterface|MockInterface|null $translatorMock;

    protected ?RenderableThrowableMetadataLoader $loader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translatorMock = Mockery::mock(TranslatorInterface::class);
        $this->loader         = new RenderableThrowableMetadataLoader($this->translatorMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->translatorMock = null;
        $this->loader         = null;
    }

    public function testItSupportsInstancesOfRenderableThrowable(): void
    {
        self::assertFalse($this->loader->supports(new Exception()));
        self::assertTrue($this->loader->supports(new class extends Exception implements RenderableThrowableInterface {
            public function getMetadata(TranslatorInterface $translator): ThrowableMetadata
            {
                return new ThrowableMetadata(false, 500, '');
            }
        }));
    }

    public function testItLoadsMetadataFromThrowable(): void
    {
        $throwable = new class extends Exception implements RenderableThrowableInterface {
            public function getMetadata(TranslatorInterface $translator): ThrowableMetadata
            {
                return new ThrowableMetadata(false, 500, '');
            }
        };

        self::assertInstanceOf(ThrowableMetadata::class, $this->loader->load($throwable));
    }
}
