<?php

namespace App\Tests\Unit\Service\ExceptionHandler\Configurator;

use App\Service\ExceptionHandler\Configurator\StaticListMetadataLoaderConfigurator;
use App\Service\ExceptionHandler\Loaders\StaticListMetadataLoader;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class StaticListMetadataLoaderConfiguratorTest extends MockeryTestCase
{
    public function testItConfigureStaticListMetadataLoader(): void
    {
        $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'static_list_metadata.php';
        file_put_contents($tempFile, '<?php return [];');

        $loader = Mockery::mock(StaticListMetadataLoader::class);
        $loader->expects('setFactories')->with([])->andReturns();

        $configurator = new StaticListMetadataLoaderConfigurator($tempFile);
        $configurator->configure($loader);

        @unlink($tempFile);
    }
}
