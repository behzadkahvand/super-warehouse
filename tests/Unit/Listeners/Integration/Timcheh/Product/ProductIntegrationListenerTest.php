<?php

namespace App\Tests\Unit\Listeners\Integration\Timcheh\Product;

use App\Entity\Product;
use App\Listeners\Integration\Timcheh\Product\ProductIntegrationListener;
use App\Messaging\Messages\Event\Integration\Timcheh\Product\UpdateProductInTimchehMessage;
use App\Service\Integration\IntegrationablePropertiesDiscoverService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\UnitOfWork;
use Mockery;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProductIntegrationListenerTest extends BaseUnitTestCase
{
    protected ?MessageBusInterface $bus;

    protected ?Product $product;

    private ?IntegrationablePropertiesDiscoverService $discoverService;

    private ?LifecycleEventArgs $lifecycleEventArgs;

    private ?EntityManagerInterface $manager;

    private ?UnitOfWork $unitOfWork;

    private ?ProductIntegrationListener $listener;

    public function setUp(): void
    {
        $this->bus = Mockery::mock(MessageBusInterface::class);
        $this->discoverService = Mockery::mock(IntegrationablePropertiesDiscoverService::class);
        $this->lifecycleEventArgs = Mockery::mock(LifecycleEventArgs::class);
        $this->manager = Mockery::mock(EntityManagerInterface::class);
        $this->unitOfWork = Mockery::mock(UnitOfWork::class);
        $this->listener = new ProductIntegrationListener($this->bus, $this->discoverService);
        $this->product = Mockery::mock(Product::class);
    }

    public function testOnPostUpdate(): void
    {
        $this->callProductMockExpectations();

        $this->discoverService->expects('getIntegrationableProperties')
            ->with(get_class($this->product), ['timcheh.product.update'])
            ->andReturns(['id', 'title', 'width', 'height', 'length', 'weight']);

        $this->lifecycleEventArgs->expects('getEntityManager')
            ->withNoArgs()
            ->andReturns($this->manager);

        $this->bus->expects('dispatch')
            ->with(Mockery::type(UpdateProductInTimchehMessage::class))
            ->andReturn(new Envelope(new \stdClass()));

        $this->manager->expects('getUnitOfWork')->withNoArgs()->andReturns($this->unitOfWork);

        $this->unitOfWork->expects('computeChangeSets')->withNoArgs()->andReturns();
        $this->unitOfWork->expects('getEntityChangeSet')
            ->with($this->product)
            ->andReturns(
                [
                    "width" => [
                        0 => 10,
                        1 => 12,
                    ],
                    'height' => [
                        0 => 10,
                        1 => 12,
                    ],
                ]
            );

        $this->listener->onPostUpdate($this->product, $this->lifecycleEventArgs);
    }

    public function testOnPostUpdateWithEmptyIntegrationableProperties(): void
    {
        $this->discoverService->expects('getIntegrationableProperties')
            ->with(get_class($this->product), ['timcheh.product.update'])
            ->andReturns([]);

        $this->listener->onPostUpdate($this->product, $this->lifecycleEventArgs);
    }

    public function testOnPostUpdateWithNotChangedIntegrationableProperties(): void
    {
        $this->discoverService->expects('getIntegrationableProperties')
            ->with(get_class($this->product), ['timcheh.product.update'])
            ->andReturns(["title", "image"]);

        $this->lifecycleEventArgs->expects('getEntityManager')->withNoArgs()->andReturns($this->manager);

        $this->manager->expects('getUnitOfWork')->withNoArgs()->andReturns($this->unitOfWork);

        $this->unitOfWork->expects('computeChangeSets')->withNoArgs()->andReturns();
        $this->unitOfWork->expects('getEntityChangeSet')
            ->with($this->product)
            ->andReturns([]);

        $this->listener->onPostUpdate($this->product, $this->lifecycleEventArgs);
    }

    private function callProductMockExpectations(): void
    {
        $this->product->expects('getId')
            ->withNoArgs()
            ->andReturn(1);

        $this->product->expects('getHeight')
            ->withNoArgs()
            ->andReturn(1);

        $this->product->expects('getWidth')
            ->withNoArgs()
            ->andReturn(1);

        $this->product->expects('getWeight')
            ->withNoArgs()
            ->andReturn(1);

        $this->product->expects('getLength')
            ->withNoArgs()
            ->andReturn(1);
    }
}
