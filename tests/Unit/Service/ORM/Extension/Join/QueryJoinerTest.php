<?php

namespace App\Tests\Unit\Service\ORM\Extension\Join;

use App\Service\ORM\Extension\Join\QueryJoiner;
use App\Service\ORM\Extension\Join\QueryJoinerData;
use App\Service\ORM\QueryContext;
use Doctrine\ORM\QueryBuilder;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class QueryJoinerTest extends MockeryTestCase
{
    public function testItThrowExceptionIfJoinTypeIsInvalid(): void
    {
        $invalidJoinType = 3;
        $joiner          = new QueryJoiner();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid join type given.');

        $joiner->join(
            \Mockery::mock(QueryBuilder::class),
            \Mockery::mock(QueryContext::class),
            new QueryJoinerData('foo', 'bar', 'Foo', 'Bar'),
            $invalidJoinType
        );
    }

    public function testItApplyInnerJoin(): void
    {
        $entityClass   = 'Foo';
        $relationClass = 'Bar';
        $entityAlias   = 'foo';
        $relationField = 'bar';

        $queryBuilder = \Mockery::mock(QueryBuilder::class);
        $queryBuilder->expects('innerJoin')->with('foo.bar', \Mockery::type('string'))->andReturnSelf();
        $queryBuilder->expects('addSelect')->with(\Mockery::type('string'))->andReturnSelf();

        $context = \Mockery::mock(QueryContext::class);
        $context->expects('setAlias')
                ->with($entityClass, $relationClass, \Mockery::type('string'))
                ->andReturnSelf();
        $context->expects('changeCurrentAlias')->with(\Mockery::type('string'))->andReturnSelf();

        $joiner = new QueryJoiner();
        $result = $joiner->join(
            $queryBuilder,
            $context,
            new QueryJoinerData($entityAlias, $relationField, $entityClass, $relationClass),
            QueryJoiner::JOIN_TYPE_INNER
        );

        self::assertIsArray($result);
        self::assertCount(2, $result);
        self::assertEquals($relationClass, $result[0]);
        self::assertTrue(is_string($result[1]));
    }

    public function testItApplyLeftJoin(): void
    {
        $entityClass   = 'Foo';
        $relationClass = 'Bar';
        $entityAlias   = 'foo';
        $relationField = 'bar';

        $queryBuilder = \Mockery::mock(QueryBuilder::class);
        $queryBuilder->expects('leftJoin')->with('foo.bar', \Mockery::type('string'))->andReturnSelf();
        $queryBuilder->allows('addSelect')->never();

        $context = \Mockery::mock(QueryContext::class);
        $context->expects('setAlias')
                ->with($entityClass, $relationClass, \Mockery::type('string'))
                ->andReturnSelf();
        $context->expects('changeCurrentAlias')->with(\Mockery::type('string'))->andReturnSelf();

        $joiner = new QueryJoiner();
        $result = $joiner->join(
            $queryBuilder,
            $context,
            new QueryJoinerData($entityAlias, $relationField, $entityClass, $relationClass),
            QueryJoiner::JOIN_TYPE_LEFT
        );

        self::assertIsArray($result);
        self::assertCount(2, $result);
        self::assertEquals($relationClass, $result[0]);
        self::assertTrue(is_string($result[1]));
    }
}
