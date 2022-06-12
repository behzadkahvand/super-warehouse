<?php

namespace App\Tests\Unit\Service\MongoFilter\Filters\Integration\EventStore;

use App\Service\MongoFilter\FilterPayload;
use App\Service\MongoFilter\Filters\Integration\EventStore\SourceServiceNameFilter;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ODM\MongoDB\Query\Builder;
use Mockery;

final class SourceServiceNameFilterTest extends BaseUnitTestCase
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
        $this->builder->expects('equals')
            ->with(Mockery::type('string'))
            ->andReturnSelf();

        $requestData = ['filter' => ['event_store.sourceServiceName' => 'test']];
        $payload = (new FilterPayload())
            ->setQueryBuilder($this->builder)
            ->setRequestFilters($requestData);

        $filter = new SourceServiceNameFilter();
        $filter($payload);
    }

    public function testDoInvokeNotValid(): void
    {
        $requestData = ['filter' => ['event_store.test' => '10']];
        $payload = (new FilterPayload())
            ->setQueryBuilder($this->builder)
            ->setRequestFilters($requestData);

        $filter = new SourceServiceNameFilter();
        $result = $filter($payload);

        self::assertEquals($payload, $result);
    }

    public function testPriority(): void
    {
        self::assertEquals(80, SourceServiceNameFilter::getPriority());
    }
}
