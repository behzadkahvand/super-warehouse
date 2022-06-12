<?php

namespace App\Service\ExceptionHandler\Configurator;

use App\Service\ExceptionHandler\Loaders\StaticListMetadataLoader;

final class StaticListMetadataLoaderConfigurator
{
    public function __construct(private string $metadataFactoryList)
    {
    }

    public function configure(StaticListMetadataLoader $loader): void
    {
        $loader->setFactories(include $this->metadataFactoryList);
    }
}
