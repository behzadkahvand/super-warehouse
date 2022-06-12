<?php

namespace App\Tests\Unit\Service\StatusTransition\AllowTransitions\Receipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\Service\StatusTransition\AllowTransitions\Receipt\STOutboundReceiptAllowedTransition;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class STOutboundReceiptAllowedTransitionTest extends MockeryTestCase
{
    private STOutboundReceiptAllowedTransition|null $receiptAllowedTransition;

    protected function setUp(): void
    {
        parent::setUp();
        $this->receiptAllowedTransition = new STOutboundReceiptAllowedTransition();
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

        self::assertEquals(ReceiptStatusDictionary::DRAFT, $result->getDefault());
        self::assertEquals([ReceiptStatusDictionary::APPROVED], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::DRAFT));
        self::assertEquals([ReceiptStatusDictionary::READY_TO_PICK], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::APPROVED));
        self::assertEquals([ReceiptStatusDictionary::PICKING], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::READY_TO_PICK));
        self::assertEquals([ReceiptStatusDictionary::DONE], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::PICKING));
        self::assertEquals([], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::DONE));
    }
}
