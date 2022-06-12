<?php

namespace App\Tests\Unit\Service\ExceptionHandler\Loaders;

use App\Service\ExceptionHandler\Annotations\Metadata;
use App\Service\ExceptionHandler\Loaders\AnnotationMetadataLoader;
use App\Service\ExceptionHandler\ThrowableMetadata;
use Doctrine\Common\Annotations\Reader;
use Exception;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AnnotationMetadataLoaderTest extends MockeryTestCase
{
    private TranslatorInterface|LegacyMockInterface|MockInterface|null $translatorMock;

    private LegacyMockInterface|MockInterface|Reader|null $readerMock;

    private ?AnnotationMetadataLoader $loader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translatorMock = Mockery::mock(TranslatorInterface::class);
        $this->readerMock     = Mockery::mock(Reader::class);

        $this->loader = new AnnotationMetadataLoader($this->readerMock, $this->translatorMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->translatorMock = null;
        $this->readerMock     = null;
        $this->loader         = null;

        Mockery::close();
    }

    public function testItDoesNotSupportThrowablesWithoutMappingAnnotation(): void
    {
        $this->readerMock->expects('getClassAnnotation')
                         ->with(Mockery::type(ReflectionClass::class), Metadata::class)
                         ->andReturnNull();

        self::assertFalse($this->loader->supports(new Exception()));
    }

    public function testItSupportThrowablesWithMappingAnnotation(): void
    {
        $this->readerMock->expects('getClassAnnotation')
                         ->with(Mockery::type(ReflectionClass::class), Metadata::class)
                         ->andReturns(Mockery::mock(Metadata::class));

        self::assertTrue($this->loader->supports(new Exception()));
    }

    public function testItLoadsThrowableMetadataFromClassAnnotation(): void
    {
        $metadata = new Metadata([]);

        $this->readerMock->expects('getClassAnnotation')
                         ->with(Mockery::type(ReflectionClass::class), Metadata::class)
                         ->andReturns($metadata);

        self::assertInstanceOf(ThrowableMetadata::class, $this->loader->load(new Exception()));
    }

    public function testItTranslateMessageWithoutData(): void
    {
        $throwable      = new Exception();
        $translationKey = 'translation_key';
        $metadata       = new Metadata([
            'detail' => [
                'translation' => ['key' => $translationKey],
            ],
        ]);

        $this->readerMock->expects('getClassAnnotation')
                         ->with(Mockery::type(ReflectionClass::class), Metadata::class)
                         ->andReturns($metadata);

        $this->translatorMock->expects('trans')
                             ->with($translationKey, [], 'exceptions', 'fa')
                             ->andReturns('پیام ترجمه شده');

        self::assertInstanceOf(ThrowableMetadata::class, $this->loader->load($throwable));
    }

    public function testItTranslateMessageWithData(): void
    {
        $parameters = [
            'foo' => 'bar'
        ];

        $throwable = new class ($parameters) extends Exception {
            private array $parameters;

            public function __construct(array $parameters)
            {
                parent::__construct('', 0, null);
                $this->parameters = $parameters;
            }

            public function getData()
            {
                return $this->parameters;
            }
        };

        $translationKey = 'translation_key';
        $metadata       = new Metadata([
            'detail' => [
                'translation' => [
                    'key'        => $translationKey,
                    'dataMethod' => 'getData',
                ],
            ],
        ]);

        $this->readerMock->expects('getClassAnnotation')
                         ->with(Mockery::type(ReflectionClass::class), Metadata::class)
                         ->andReturns($metadata);

        $this->translatorMock->expects('trans')
                             ->with($translationKey, $parameters, 'exceptions', 'fa')
                             ->andReturns('پیام ترجمه شده');

        self::assertInstanceOf(ThrowableMetadata::class, $this->loader->load($throwable));
    }
}
