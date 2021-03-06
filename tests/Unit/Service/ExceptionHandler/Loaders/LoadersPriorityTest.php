<?php

namespace App\Tests\Unit\Service\ExceptionHandler\Loaders;

use App\Service\ExceptionHandler\Loaders\AnnotationMetadataLoader;
use App\Service\ExceptionHandler\Loaders\InternalServerErrorMetadataLoader;
use App\Service\ExceptionHandler\Loaders\RenderableThrowableMetadataLoader;
use App\Service\ExceptionHandler\Loaders\StaticListMetadataLoader;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use SplPriorityQueue;

final class LoadersPriorityTest extends MockeryTestCase
{
    public function testPriorities(): void
    {
        $renderableThrowableMetadataLoader = Mockery::mock(RenderableThrowableMetadataLoader::class);
        $staticListMetadataLoader          = Mockery::mock(StaticListMetadataLoader::class);
        $annotationMetadataLoader          = Mockery::mock(AnnotationMetadataLoader::class);
        $internalServerErrorMetadataLoader = Mockery::mock(InternalServerErrorMetadataLoader::class);

        $q = new SplPriorityQueue();
        $q->insert($renderableThrowableMetadataLoader, RenderableThrowableMetadataLoader::getPriority());
        $q->insert($staticListMetadataLoader, StaticListMetadataLoader::getPriority());
        $q->insert($annotationMetadataLoader, AnnotationMetadataLoader::getPriority());
        $q->insert($internalServerErrorMetadataLoader, InternalServerErrorMetadataLoader::getPriority());

        self::assertSame($renderableThrowableMetadataLoader, $q->extract());
        self::assertSame($staticListMetadataLoader, $q->extract());
        self::assertSame($annotationMetadataLoader, $q->extract());
        self::assertSame($internalServerErrorMetadataLoader, $q->extract());
    }
}
