<?php

namespace App\Tests\Unit\Service\ORM\Extension\Utils;

use App\Service\ORM\Extension\Utils\OperatorAndValueExtractor;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class OperatorAndValueExtractorTest extends MockeryTestCase
{
    /**
     * @dataProvider normalizeValueProvider
     */
    public function testItNormalizeValues(string $value, $expectedValue): void
    {
        $extractor = new OperatorAndValueExtractor();

        self::assertEquals($expectedValue, $extractor->extract($value));
    }

    /**
     * @dataProvider operatorAndValueProvider
     */
    public function testItExtractOperatorAndValues(array $value, $expectedValue): void
    {
        $extractor = new OperatorAndValueExtractor();

        self::assertEquals($expectedValue, $extractor->extract($value));
    }

    public function normalizeValueProvider(): array
    {
        return [
            ['12', ['=' => 12]],
            ['1,2', ['=' => '1,2']],
            ['1', ['=' => 1]],
            ['null', ['=' => null]],
            ['false', ['=' => false]],
            ['true', ['=' => true]],
            ['foo', ['=' => 'foo']],
        ];
    }

    public function operatorAndValueProvider(): array
    {
        return [
            [['btn' => '1,2'], ['BETWEEN' => [1, 2]]],
            [['in' => '1,2'], ['IN' => [1, 2]]],
            [['nin' => '1,2'], ['NOT_IN' => [1, 2]]],
            [['like' => '%foo%'], ['LIKE' => '%foo%']],
            [['gt' => '10'], ['>' => 10]],
            [['gte' => '10'], ['>=' => 10]],
            [['lt' => '10'], ['<' => 10]],
            [['lte' => '10'], ['<=' => 10]],
            [['neq' => '10'], ['!=' => 10]],
            [['invalid_operator' => '10'], ['=' => 10]],
        ];
    }
}
