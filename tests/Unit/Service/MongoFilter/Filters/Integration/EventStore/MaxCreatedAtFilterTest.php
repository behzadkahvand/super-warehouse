<?php

namespace App\Tests\Unit\Service\MongoFilter\Filters\Integration\EventStore;

use App\Service\MongoFilter\FilterPayload;
use App\Service\MongoFilter\Filters\Integration\EventStore\MaxCreatedAtFilter;
use App\Tests\Unit\BaseUnitTestCase;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\Query\Builder;
use Mockery;

final class MaxCreatedAtFilterTest extends BaseUnitTestCase
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
        $this->builder->expects('lte')
            ->with(Mockery::type(DateTimeInterface::class))
            ->andReturnSelf();

        $requestData = ['filter' => ['event_store.createdAt.max' => '2022-12-10 23:59:59']];
        $payload = (new FilterPayload())
            ->setQueryBuilder($this->builder)
            ->setRequestFilters($requestData);

        $filter = new MaxCreatedAtFilter();
        $filter($payload);
    }

    public function testDoInvokeNotValid(): void
    {
        $requestData = ['filter' => ['event_store.test' => '10']];
        $payload = (new FilterPayload())
            ->setQueryBuilder($this->builder)
            ->setRequestFilters($requestData);

        $filter = new MaxCreatedAtFilter();
        $result = $filter($payload);

        self::assertEquals($payload, $result);
    }

    public function testPriority(): void
    {
        self::assertEquals(60, MaxCreatedAtFilter::getPriority());
    }
}
