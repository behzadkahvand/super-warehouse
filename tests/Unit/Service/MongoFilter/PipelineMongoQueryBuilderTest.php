<?php

namespace App\Tests\Unit\Service\MongoFilter;

use App\Document\InventoryPriceHistoryLog;
use App\Service\MongoFilter\FilterPayload;
use App\Service\MongoFilter\PipelineMongoQueryBuilder;
use App\Service\Pipeline\PipelineStageInterface;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Query\Builder;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Tightenco\Collect\Support\Collection;

class PipelineMongoQueryBuilderTest extends BaseUnitTestCase
{
    protected ?Builder $builderMock;

    protected ?DocumentManager $managerMock;

    protected ?PipelineStageInterface $pipelineStageMock;

    protected ?PipelineMongoQueryBuilder $pipelineMongoQueryBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builderMock = Mockery::mock(Builder::class);
        $this->managerMock = Mockery::mock(DocumentManager::class);
        $this->pipelineStageMock = Mockery::mock(PipelineStageInterface::class);

        $this->pipelineMongoQueryBuilder = new PipelineMongoQueryBuilder(
            [$this->pipelineStageMock],
            $this->managerMock
        );
    }

    public function testItCanCallFilter(): void
    {
        $sourceClass = InventoryPriceHistoryLog::class;

        $this->managerMock->shouldReceive('createQueryBuilder')
            ->once()
            ->with($sourceClass)
            ->andReturn($this->builderMock);

        $this->pipelineStageMock->shouldReceive('__invoke')
            ->once()
            ->with(Mockery::type(FilterPayload::class))
            ->andReturnUsing(
                function (FilterPayload $payload) {
                    return $payload->setQueryBuilder($this->builderMock);
                }
            );

        $requestData = [
            'filter' => [
                'inventory_price.product_id' => 44648,
                'inventory_price.month.min' => 1,
            ]
        ];

        $result = $this->pipelineMongoQueryBuilder->filter($sourceClass, $requestData);

        self::assertInstanceOf(Builder::class, $result);
    }
}
