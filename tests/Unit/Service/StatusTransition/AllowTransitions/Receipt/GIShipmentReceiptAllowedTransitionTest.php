<?php

namespace App\Tests\Unit\Service\StatusTransition\AllowTransitions\Receipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\Service\StatusTransition\AllowTransitions\Receipt\GIShipmentReceiptAllowedTransition;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class GIShipmentReceiptAllowedTransitionTest extends MockeryTestCase
{
    private GIShipmentReceiptAllowedTransition|null $receiptAllowedTransition;

    protected function setUp(): void
    {
        parent::setUp();
        $this->receiptAllowedTransition = new GIShipmentReceiptAllowedTransition();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->receiptAllowedTransition = null;
        Mockery::close();
    }

    public function testItCanCallInvoke(): void
    {
        $result = $this->receiptAllowedTransition->__invoke();

        self::assertEquals(ReceiptStatusDictionary::RESERVED, $result->getDefault());
        self::assertEquals(
            [
                ReceiptStatusDictionary::WAITING_FOR_SUPPLY,
                ReceiptStatusDictionary::APPROVED,
                ReceiptStatusDictionary::CANCELED,
            ],
            $result->getAllowedTransitionsFor(ReceiptStatusDictionary::RESERVED)
        );
        self::assertEquals(
            [ReceiptStatusDictionary::APPROVED, ReceiptStatusDictionary::CANCELED],
            $result->getAllowedTransitionsFor(ReceiptStatusDictionary::WAITING_FOR_SUPPLY)
        );
        self::assertEquals(
            [ReceiptStatusDictionary::READY_TO_PICK, ReceiptStatusDictionary::CANCELED],
            $result->getAllowedTransitionsFor(ReceiptStatusDictionary::APPROVED)
        );
        self::assertEquals(
            [ReceiptStatusDictionary::PICKING],
            $result->getAllowedTransitionsFor(ReceiptStatusDictionary::READY_TO_PICK)
        );
        self::assertEquals(
            [ReceiptStatusDictionary::DONE],
            $result->getAllowedTransitionsFor(ReceiptStatusDictionary::PICKING)
        );
        self::assertEquals([], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::DONE));
    }
}
