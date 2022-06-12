<?php

namespace App\Tests\Unit\Service\ExceptionHandler\Loaders;

use App\Service\ExceptionHandler\Loaders\InternalServerErrorMetadataLoader;
use App\Service\ExceptionHandler\ThrowableMetadata;
use Exception;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Symfony\Component\HttpFoundation\Response;

final class InternalServerErrorMetadataLoaderTest extends MockeryTestCase
{
    public function testItSupportAnyExceptionType(): void
    {
        $loader = new InternalServerErrorMetadataLoader();

        self::assertTrue($loader->supports(new Exception()));
    }

    public function testItLoadsMetadata(): void
    {
        $loader   = new InternalServerErrorMetadataLoader();
        $metadata = $loader->load(new Exception());

        self::assertInstanceOf(ThrowableMetadata::class, $metadata);
        self::assertTrue($metadata->isVisibleForUsers());
        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $metadata->getStatusCode());
        self::assertEquals(Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR], $metadata->getTitle());
    }
}
