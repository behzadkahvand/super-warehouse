<?php

namespace App\Tests\Unit\Service\MongoFilter\Filters\Integration\EventStore;

use App\Service\MongoFilter\FilterPayload;
use App\Service\MongoFilter\Filters\Integration\EventStore\PayloadFilter;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ODM\MongoDB\Query\Builder;
use Mockery;

final class PayloadFilterTest extends BaseUnitTestCase
{
    protected ?Builder $builder;

    public function setUp(): void
    {
        parent::setUp();
        $this->builder = Mockery::mock(Builder::class);
    }

    public function testDoInvoke(): void
    {
        $this->builder->shouldReceive('field')
            ->times(3)
            ->with(Mockery::type('string'))
            ->andReturnSelf();

        $this->builder->expects('equals')
            ->with(Mockery::type('int'))
            ->andReturnSelf();

        $this->builder->expects('equals')
            ->with(Mockery::type('bool'))
            ->andReturnSelf();

        $this->builder->expects('equals')
            ->with(Mockery::type('string'))
            ->andReturnSelf();

        $requestData = ['filter' => ['event_store.payload' => ['width' => 'true', 'height' => '10', 'length' => '12cm']]];
        $payload = (new FilterPayload())
            ->setQueryBuilder($this->builder)
            ->setRequestFilters($requestData);

        $filter = new PayloadFilter();
        $filter($payload);
    }

    public function testDoInvokeNotValid(): void
    {
        $requestData = ['filter' => ['event_store.test' => '10']];
        $payload = (new FilterPayload())
            ->setQueryBuilder($this->builder)
            ->setRequestFilters($requestData);

        $filter = new PayloadFilter();
        $result = $filter($payload);

        self::assertEquals($payload, $result);
    }

    public function testPriority(): void
    {
        self::assertEquals(50, PayloadFilter::getPriority());
    }
}
