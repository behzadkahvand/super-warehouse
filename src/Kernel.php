<?php

namespace App;

use App\DependencyInjection\Compiler\ExceptionMetadataLoaderFactoryPass;
use App\DependencyInjection\Compiler\PipelineStagePass;
use App\DependencyInjection\Compiler\StateTransitionSubscriberPass;
use App\Messaging\Messages\Event\Integration\Timcheh\ConsumerMessageInterface;
use App\Service\ExceptionHandler\Factories\AbstractMetadataFactory;
use App\Service\ExceptionHandler\Loaders\MetadataLoaderInterface;
use App\Service\Integration\Timcheh\LogStore\LogStoreInterface;
use App\Service\ORM\Extension\QueryBuilderExtensionInterface;
use App\Service\PickList\BugReport\Status\PickListBugReportStatusInterface;
use App\Service\PickList\HandHeld\Picking\PickingResolverInterface;
use App\Service\Pipeline\PipelineStageInterface;
use App\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods\CapacityMethodCheckInterface;
use App\Service\PullList\HandHeld\StowingProcess\StowingResolverInterface;
use App\Service\PullList\HandHeld\StowingProcess\StowingStrategy\StowingStrategyInterface;
use App\Service\Relocate\Stowing\RelocateBinResolverInterface;
use App\Service\Relocate\Stowing\RelocateItemResolverInterface;
use App\Service\SellerPackage\SellerPackageStatus\SellerPackageStatusInterface;
use App\Service\SellerPackageItem\SellerPackageItemStatus\SellerPackageItemStatusInterface;
use App\Service\StatusTransition\Subscribers\StateSubscriberInterface;
use App\Service\Utils\Error\ErrorExtractorInterface;
use App\Service\Warehouse\PickingStrategy\PickingStrategyInterface;
use App\Service\WarehouseStorageBin\AutoGenerate\Creators\CreatorInterface;
use App\Service\WarehouseStorageBin\AutoGenerate\Editors\EditorInterface;
use App\Service\WarehouseStorageBin\AutoGenerate\Handlers\HandlerInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private array $autoconfigurations = [
        ErrorExtractorInterface::class          => 'app.error_extractor',
        QueryBuilderExtensionInterface::class   => 'app.query_builder_filter_extension',
        HandlerInterface::class                 => 'app.warehouse_storage_bin.auto_generate.handler',
        CreatorInterface::class                 => 'app.warehouse_storage_bin.auto_generate.creator',
        EditorInterface::class                  => 'app.warehouse_storage_bin.auto_generate.editor',
        MetadataLoaderInterface::class          => 'app.exception_handler.metadata_loader',
        AbstractMetadataFactory::class          => 'app.exception_handler.metadata_factory',
        SellerPackageStatusInterface::class     => 'app.seller_package.seller_package_service',
        SellerPackageItemStatusInterface::class => 'app.seller_package_item.seller_package_item_service',
        StateSubscriberInterface::class         => 'app.state.transition.subscribers',
        PickingStrategyInterface::class         => 'app.warehouse.warehouse_picking_strategy_service',
        PipelineStageInterface::class           => 'app.pipeline_stage',
        PickListBugReportStatusInterface::class => 'app.pick_list.pick_list_bug_report_status_service',
        PickingResolverInterface::class         => 'app.handHeld.picking_resolvers',
        StowingResolverInterface::class         => 'app.handHeld.stowing_resolvers',
        StowingStrategyInterface::class         => 'app.handHeld.stowing_strategy.check',
        CapacityMethodCheckInterface::class     => 'app.handHeld.stowing.capacity_method.check',
        RelocateItemResolverInterface::class    => 'app.relocate.item_resolvers',
        RelocateBinResolverInterface::class     => 'app.relocate.bin_resolvers',
        ConsumerMessageInterface::class         => 'app.messenger.messages.event.integration.consumer',
        LogStoreInterface::class                => 'app.integration.log_store.resolvers',
    ];

    protected function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ExceptionMetadataLoaderFactoryPass());
        $container->addCompilerPass(new StateTransitionSubscriberPass());
        $container->addCompilerPass(new PipelineStagePass());

        foreach ($this->autoconfigurations as $interface => $tag) {
            $container->registerForAutoconfiguration($interface)->addTag($tag);
        }
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/' . $this->environment . '/*.yaml');

        if (is_file(\dirname(__DIR__) . '/config/services.yaml')) {
            $container->import('../config/services.yaml');
            $container->import('../config/{services}_' . $this->environment . '.yaml');
        } elseif (is_file($path = \dirname(__DIR__) . '/config/services.php')) {
            (require $path)($container->withPath($path), $this);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/' . $this->environment . '/*.yaml');
        $routes->import('../config/{routes}/*.yaml');

        if (is_file(\dirname(__DIR__) . '/config/routes.yaml')) {
            $routes->import('../config/routes.yaml');
        } elseif (is_file($path = \dirname(__DIR__) . '/config/routes.php')) {
            (require $path)($routes->withPath($path), $this);
        }
    }
}
