<?php

namespace App\Tests\Unit\Service\PullList\ReceiptItemAddList\Stages;

use App\Dictionary\ReceiptStatusDictionary;
use App\Service\PullList\ReceiptItemAddList\SearchPayload;
use App\Service\PullList\ReceiptItemAddList\Stages\DefaultFilterAndSortStage;
use App\Tests\Unit\BaseUnitTestCase;

class DefaultFilterAndSortStageTest extends BaseUnitTestCase
{
    protected ?DefaultFilterAndSortStage $defaultFilterAndSortStage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultFilterAndSortStage = new DefaultFilterAndSortStage();
    }

    public function testGetTagAndPriority(): void
    {
        self::assertEquals(
            'app.pipeline_stage.pull_list.receipt_item.add_list',
            $this->defaultFilterAndSortStage::getTag()
        );
        self::assertEquals(95, $this->defaultFilterAndSortStage::getPriority());
    }

    public function testItCanAddDefaultFiltersAndSorts(): void
    {
        $payload = new SearchPayload(2, ['receiptId' => 1], ['sorts']);

        $this->defaultFilterAndSortStage->__invoke($payload);

        self::assertEquals([
            'receiptId' => 1,
            'status'    => ReceiptStatusDictionary::READY_TO_STOW
        ], $payload->getFilters());

        self::assertEquals(['-id', '-receipt.id', 'sorts'], $payload->getSorts());
    }
}
