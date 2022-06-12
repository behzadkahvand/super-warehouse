<?php

namespace App\Tests\Unit\Listeners;

use App\Listeners\ExceptionListener;
use App\Service\ExceptionHandler\MetadataLoader;
use App\Service\ExceptionHandler\ThrowableMetadata;
use Exception;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

final class ExceptionListenerTest extends MockeryTestCase
{
    protected LegacyMockInterface|MetadataLoader|MockInterface|null $metadataLoaderMock;

    protected ?ExceptionListener $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->metadataLoaderMock = Mockery::mock(MetadataLoader::class);

        $this->listener = new ExceptionListener($this->metadataLoaderMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->listener           = null;
        $this->metadataLoaderMock = null;

        Mockery::close();
    }

    public function testGettingSubscribedEvents(): void
    {
        $expected = [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];

        self::assertEquals($expected, ExceptionListener::getSubscribedEvents());
    }

    public function testItReturnOnInstancesOfHttpExceptionInterface(): void
    {
        $throwable = new class () extends Exception implements HttpExceptionInterface {
            public function getStatusCode(): void
            {
            }

            public function getHeaders(): void
            {
            }
        };

        $event = new ExceptionEvent(
            Mockery::mock(KernelInterface::class),
            Mockery::mock(Request::class),
            KernelInterface::MAIN_REQUEST,
            $throwable
        );

        $this->metadataLoaderMock->allows('getMetadata')->never();

        $this->listener->onKernelException($event);
    }

    public function testItConvertThrowableToInternalServerErrorForInvisibleThrowables(): void
    {
        $throwable = new Exception();
        $event     = new ExceptionEvent(
            Mockery::mock(KernelInterface::class),
            Mockery::mock(Request::class),
            KernelInterface::MAIN_REQUEST,
            $throwable
        );

        $this->metadataLoaderMock->expects('getMetadata')
                                 ->with($throwable)
                                 ->andReturns(new ThrowableMetadata(false, 500, 'invisible throwable'));

        $this->listener->onKernelException($event);

        $exception = $event->getThrowable();
        self::assertInstanceOf(HttpException::class, $exception);
        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getStatusCode());
        self::assertEquals(Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR], $exception->getMessage());
    }

    public function testItConvertThrowableToHttpException(): void
    {
        $throwable = new Exception();
        $event     = new ExceptionEvent(
            Mockery::mock(KernelInterface::class),
            Mockery::mock(Request::class),
            KernelInterface::MAIN_REQUEST,
            $throwable
        );

        $this->metadataLoaderMock->expects('getMetadata')
                                 ->with($throwable)
                                 ->andReturns(new ThrowableMetadata(true, 404, 'Not Found'));

        $this->listener->onKernelException($event);

        $exception = $event->getThrowable();
        self::assertInstanceOf(HttpException::class, $exception);
        self::assertEquals(Response::HTTP_NOT_FOUND, $exception->getStatusCode());
        self::assertEquals(Response::$statusTexts[Response::HTTP_NOT_FOUND], $exception->getMessage());
    }
}
