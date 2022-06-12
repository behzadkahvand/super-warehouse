<?php

namespace App\Tests\Unit\Service\MongoFilter\Filters\Integration\LogStore;

use App\Service\MongoFilter\FilterPayload;
use App\Service\MongoFilter\Filters\Integration\LogStore\StatusFilter;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ODM\MongoDB\Query\Builder;
use Mockery;

final class StatusFilterTest extends BaseUnitTestCase
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

        $requestData = ['filter' => ['log_store.status' => 'PROCESSED']];
        $payload = (new FilterPayload())
            ->setQueryBuilder($this->builder)
            ->setRequestFilters($requestData);

        $filter = new StatusFilter();
        $filter($payload);
    }

    public function testDoInvokeNotValid(): void
    {
        $requestData = ['filter' => ['log_store.test' => '10']];
        $payload = (new FilterPayload())
            ->setQueryBuilder($this->builder)
            ->setRequestFilters($requestData);

        $filter = new StatusFilter();
        $result = $filter($payload);

        self::assertEquals($payload, $result);
    }

    public function testPriority(): void
    {
        self::assertEquals(90, StatusFilter::getPriority());
    }
}
