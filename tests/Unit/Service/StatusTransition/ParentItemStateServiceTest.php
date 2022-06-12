<?php

namespace App\Tests\Unit\Service\StatusTransition;

use App\Dictionary\ReceiptSortedStatusDictionary;
use App\Service\StatusTransition\ParentItemStateService;
use Exception;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ParentItemStateServiceTest extends MockeryTestCase
{
    private ParentItemStateService|null $parentItemStateService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parentItemStateService = new ParentItemStateService();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->parentItemStateService = null;
        Mockery::close();
    }

    public function testFindLowestStatusItemsWhenDictionaryKeyDataNotFound(): void
    {
        $dictionary            = ReceiptSortedStatusDictionary::class;
        $dictionaryConstantKey = "test";
        $items                 = ["DRAFT"];

        self::expectException(Exception::class);

        $this->parentItemStateService->findLowestStatusItems($dictionary, $dictionaryConstantKey, $items);
    }

    public function testFindLowestStatusItemsWhenItemStatusNotFound(): void
    {
        $dictionary            = ReceiptSortedStatusDictionary::class;
        $dictionaryConstantKey = "GINONERECEIPT";
        $items                 = ["PEND"];

        self::expectException(Exception::class);

        $this->parentItemStateService->findLowestStatusItems($dictionary, $dictionaryConstantKey, $items);
    }

    public function testFindLowestStatusItemsSuccess(): void
    {
        $dictionary            = ReceiptSortedStatusDictionary::class;
        $dictionaryConstantKey = "GINONERECEIPT";
        $items                 = ["READY_TO_PICK", "APPROVED", "DONE"];

        $minStatus = $this->parentItemStateService->findLowestStatusItems($dictionary, $dictionaryConstantKey, $items);

        self::assertEquals("APPROVED", $minStatus);
    }
}
