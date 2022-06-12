<?php

namespace App\Tests\Unit\Service\ExceptionHandler\Loaders;

use App\Service\ExceptionHandler\Factories\AbstractMetadataFactory;
use App\Service\ExceptionHandler\Loaders\MetadataLoaderInterface;
use App\Service\ExceptionHandler\Loaders\StaticListMetadataLoader;
use App\Service\ExceptionHandler\ThrowableMetadata;
use Exception;
use InvalidArgumentException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;
use stdClass;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

final class StaticListMetadataLoaderTest extends MockeryTestCase
{
    protected TranslatorInterface|LegacyMockInterface|MockInterface|null $translatorMock;

    protected LegacyMockInterface|MockInterface|ContainerInterface|null $containerMock;

    protected LegacyMockInterface|MetadataLoaderInterface|MockInterface|null $fallbackLoaderMock;

    protected ?StaticListMetadataLoader $loader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translatorMock     = Mockery::mock(TranslatorInterface::class);
        $this->containerMock      = Mockery::mock(ContainerInterface::class);
        $this->fallbackLoaderMock = Mockery::mock(MetadataLoaderInterface::class);

        $this->loader = new StaticListMetadataLoader(
            $this->translatorMock,
            $this->containerMock,
            $this->fallbackLoaderMock
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->translatorMock     = null;
        $this->containerMock      = null;
        $this->fallbackLoaderMock = null;
        $this->loader             = null;

        Mockery::close();
    }

    public function testItSupportsClassHierarchyIfFactoryIsCallable(): void
    {
        $throwable = new InvalidArgumentException();

        $this->loader->setFactories([
            InvalidArgumentException::class => function () {
            },
        ]);

        self::assertTrue($this->loader->supports($throwable));

        $this->loader->setFactories([
            Exception::class => function () {
            },
        ]);

        self::assertTrue($this->loader->supports($throwable));

        $this->loader->setFactories([
            Throwable::class => function () {
            },
        ]);

        self::assertTrue($this->loader->supports($throwable));
    }

    public function testItSupportsClassHierarchyIfFactoryIsInstanceOfAbstractMetadataFactory(): void
    {
        $subClassOfAbstractMetadataFactory = new class ($this->containerMock) extends AbstractMetadataFactory {
            public function __invoke(Throwable $throwable, TranslatorInterface $translator): ThrowableMetadata
            {
                return new ThrowableMetadata(true, $throwable->getCode(), $throwable->getMessage());
            }
        };

        $this->containerMock->expects('has')
                            ->with(get_class($subClassOfAbstractMetadataFactory))
                            ->andReturnTrue();

        $throwable = new InvalidArgumentException();

        $this->loader->setFactories([
            InvalidArgumentException::class => get_class($subClassOfAbstractMetadataFactory),
        ]);

        self::assertTrue($this->loader->supports($throwable));

        $this->containerMock->expects('has')
                            ->with(get_class($subClassOfAbstractMetadataFactory))
                            ->andReturnFalse();

        self::assertFalse($this->loader->supports($throwable));
    }

    public function testItDoesNotSupportIfFactoryIsNotCallableOrInstanceOfAbstractMetadataFactory(): void
    {
        $throwable = new InvalidArgumentException();

        $this->loader->setFactories([
            InvalidArgumentException::class => get_class(new stdClass()),
        ]);

        self::assertFalse($this->loader->supports($throwable));
    }

    public function testItDoesNotSupportIfStaticListDoesNotHasFactoryForThrowable(): void
    {
        $throwable = new InvalidArgumentException();

        $this->loader->setFactories([]);

        self::assertFalse($this->loader->supports($throwable));
    }

    public function testItThrowExceptionIfFactoryIsNotCallableOrContainerDoesNotHasGivenFactory(): void
    {
        $factory   = get_class(new stdClass());
        $throwable = new InvalidArgumentException();

        $this->containerMock->expects('has')->with($factory)->andReturnFalse();

        $this->loader->setFactories([
            InvalidArgumentException::class => $factory,
        ]);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Expected a callable as throwable metadata factory got string');

        $this->loader->load($throwable);
    }

    public function testItLoadThrowableMetadataWithCallable(): void
    {
        $throwable = new InvalidArgumentException();

        $this->loader->setFactories([
            InvalidArgumentException::class => function () {
                return new ThrowableMetadata(false, 500, '', '');
            },
        ]);

        self::assertInstanceOf(ThrowableMetadata::class, $this->loader->load($throwable));
    }

    public function testItLoadThrowableMetadataWithAbstractMetadataFactory(): void
    {
        $subClassOfAbstractMetadataFactory = new class ($this->containerMock) extends AbstractMetadataFactory {
            public function __invoke(Throwable $throwable, TranslatorInterface $translator): ThrowableMetadata
            {
                return new ThrowableMetadata(false, 500, '', '');
            }
        };

        $throwable = new InvalidArgumentException();

        $this->containerMock->expects('has')
                            ->with(get_class($subClassOfAbstractMetadataFactory))
                            ->andReturnTrue();

        $this->containerMock->expects('get')
                            ->with(get_class($subClassOfAbstractMetadataFactory))
                            ->andReturns($subClassOfAbstractMetadataFactory);

        $this->loader->setFactories([
            InvalidArgumentException::class => get_class($subClassOfAbstractMetadataFactory),
        ]);

        self::assertInstanceOf(ThrowableMetadata::class, $this->loader->load($throwable));
    }

    public function testItDelegateLoadingMetadataToFallbackLoader(): void
    {
        $throwable = new InvalidArgumentException();

        $fallbackResponse = new ThrowableMetadata(true, 500, '', '');

        $this->fallbackLoaderMock->expects('load')
                                 ->with($throwable)
                                 ->andReturns($fallbackResponse);

        $this->loader->setFactories([]);

        self::assertSame($fallbackResponse, $this->loader->load($throwable));
    }

    public function testItUseInternalServerErrorLoaderAsDefaultFallbackLoader(): void
    {
        $throwable        = new InvalidArgumentException();
        $fallbackResponse = new ThrowableMetadata(true, 500, 'Internal Server Error', 'Internal Server Error');

        $this->loader = new StaticListMetadataLoader($this->translatorMock, $this->containerMock);
        $this->loader->setFactories([]);

        self::assertEquals($fallbackResponse, $this->loader->load($throwable));
    }
}
