<?php

namespace App\Service\ExceptionHandler\Loaders;

use App\Service\ExceptionHandler\ClassHierarchyTrait;
use App\Service\ExceptionHandler\Factories\AbstractMetadataFactory;
use App\Service\ExceptionHandler\ThrowableMetadata;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class StaticListMetadataLoader implements MetadataLoaderInterface
{
    use ClassHierarchyTrait;

    private array $factories = [];

    public function __construct(
        private TranslatorInterface $translator,
        private ContainerInterface $container,
        private ?MetadataLoaderInterface $fallbackLoader = null
    ) {
        $this->fallbackLoader = $fallbackLoader ?? new InternalServerErrorMetadataLoader();
    }

    public function setFactories(array $factories): void
    {
        $this->factories = $factories;
    }

    public function load(Throwable $throwable): ThrowableMetadata
    {
        foreach ($this->getClassHierarchy($throwable) as $class) {
            if (!isset($this->factories[$class])) {
                continue;
            }

            $factory = $this->factories[$class];

            if (!is_callable($factory) && (!is_string($factory) || !$this->container->has($factory))) {
                throw new RuntimeException('Expected a callable as throwable metadata factory got ' . get_debug_type($factory));
            }

            $factory = is_callable($factory) ? $factory : $this->container->get($factory);

            return $factory($throwable, $this->translator);
        }

        return $this->fallbackLoader->load($throwable);
    }

    public function supports(Throwable $throwable): bool
    {
        foreach ($this->getClassHierarchy($throwable) as $class) {
            if (isset($this->factories[$class])) {
                $factory = $this->factories[$class];

                if (is_callable($factory)) {
                    return true;
                }

                return is_string($factory)
                    && is_subclass_of($factory, AbstractMetadataFactory::class)
                    && $this->container->has($factory);
            }
        }

        return false;
    }

    public static function getPriority(): int
    {
        return 100;
    }
}
