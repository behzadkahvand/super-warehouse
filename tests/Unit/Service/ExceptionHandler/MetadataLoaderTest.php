<?php

namespace App\Tests\Unit\Service\ExceptionHandler;

use App\Service\ExceptionHandler\Loaders\MetadataLoaderInterface;
use App\Service\ExceptionHandler\MetadataLoader;
use App\Service\ExceptionHandler\ThrowableMetadata;
use Exception;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class MetadataLoaderTest extends MockeryTestCase
{
    public function testItDelegateLoadingMetadataToLoaders(): void
    {
        $throwable = new Exception();
        $metadata  = new ThrowableMetadata(false, 0, '', '');

        $loader = Mockery::mock(MetadataLoaderInterface::class);
        $loader->expects('supports')->with($throwable)->andReturnTrue();
        $loader->expects('load')->with($throwable)->andReturns($metadata);

        $metadataLoader = new MetadataLoader([$loader]);

        self::assertSame($metadata, $metadataLoader->getMetadata($throwable));
    }

    public function testItDelegateLoadingMetadataToFallbackLoader(): void
    {
        $throwable = new Exception();
        $metadata  = new ThrowableMetadata(false, 0, '', '');

        $fallbackLoader = Mockery::mock(MetadataLoaderInterface::class);
        $fallbackLoader->expects('load')->with($throwable)->andReturns($metadata);

        $metadataLoader = new MetadataLoader([], $fallbackLoader);

        self::assertSame($metadata, $metadataLoader->getMetadata($throwable));
    }
}
