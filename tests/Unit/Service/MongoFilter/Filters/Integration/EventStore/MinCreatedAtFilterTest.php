<?php

namespace App\Tests\Unit\Service\MongoFilter\Filters\Integration\EventStore;

use App\Service\MongoFilter\FilterPayload;
use App\Service\MongoFilter\Filters\Integration\EventStore\MinCreatedAtFilter;
use App\Tests\Unit\BaseUnitTestCase;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\Query\Builder;
use Mockery;

final class MinCreatedAtFilterTest extends BaseUnitTestCase
{
    protected ?Builder $builder;

    public function setUp(): void
    {
        parent::setUp();
        $this->builder = Mockery::mock(Builder::class);
    }

    public function testDoInvoke(): void
    {
        $this->builder->expects('field')
            ->with(Mockery::type('string'))
            ->andReturnSelf();
        $this->builder->expects('gte')
            ->with(Mockery::type(DateTimeInterface::class))
            ->andReturnSelf();

        $requestData = ['filter' => ['event_store.createdAt.min' => '2022-12-10 23:59:59']];
        $payload = (new FilterPayload())
            ->setQueryBuilder($this->builder)
            ->setRequestFilters($requestData);

        $filter = new MinCreatedAtFilter();
        $filter($payload);
    }

    public function testDoInvokeNotValid(): void
    {
        $requestData = ['filter' => ['event_store.test' => '10']];
        $payload = (new FilterPayload())
            ->setQueryBuilder($this->builder)
            ->setRequestFilters($requestData);

        $filter = new MinCreatedAtFilter();
        $result = $filter($payload);

        self::assertEquals($payload, $result);
    }

    public function testPriority(): void
    {
        self::assertEquals(70, MinCreatedAtFilter::getPriority());
    }
}
