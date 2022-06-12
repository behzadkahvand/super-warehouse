<?php

namespace App\Tests\Unit\Service\PullList\ReceiptItemAddList\Stages;

use App\Service\PullList\ReceiptItemAddList\Exceptions\SearchDataValidationException;
use App\Service\PullList\ReceiptItemAddList\SearchPayload;
use App\Service\PullList\ReceiptItemAddList\Stages\FilterAndSortMappingStage;
use App\Tests\Unit\BaseUnitTestCase;

class FilterAndSortMappingStageTest extends BaseUnitTestCase
{
    protected ?FilterAndSortMappingStage $filterAndSortMappingStage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filterAndSortMappingStage = new FilterAndSortMappingStage();
    }

    public function testGetTagAndPriority(): void
    {
        self::assertEquals(
            'app.pipeline_stage.pull_list.receipt_item.add_list',
            $this->filterAndSortMappingStage::getTag()
        );
        self::assertEquals(100, $this->filterAndSortMappingStage::getPriority());
    }

    public function testItHasAnExceptionWhenSortIsInvalid(): void
    {
        $payload = new SearchPayload(2, [], ['invalid']);

        self::expectException(SearchDataValidationException::class);
        self::expectExceptionCode(400);
        self::expectExceptionMessage('Receipt Item sorts is invalid!');

        $this->filterAndSortMappingStage->__invoke($payload);
    }

    public function testItHasAnExceptionWhenFilterIsInvalid(): void
    {
        $payload = new SearchPayload(2, ['invalid'], []);

        self::expectException(SearchDataValidationException::class);
        self::expectExceptionCode(400);
        self::expectExceptionMessage('Receipt Item filters is invalid!');

        $this->filterAndSortMappingStage->__invoke($payload);
    }

    public function testItCanMapAllValidFiltersAndSorts(): void
    {
        $payload = new SearchPayload(
            2,
            [
                'receiptItemId' => 7,
                'receiptId'     => 1,
                'productId'     => 4,
                'InventoryId'   => 9,
            ],
            []
        );

        $result = $this->filterAndSortMappingStage->__invoke($payload);

        self::assertInstanceOf(SearchPayload::class, $result);
        self::assertEquals(2, $result->getWarehouseId());
        self::assertEquals([
            'id'                   => 7,
            'receipt.id'           => 1,
            'inventory.product.id' => 4,
            'inventory.id'         => 9,
        ], $result->getFilters());
        self::assertEquals([], $result->getSorts());
    }
}
