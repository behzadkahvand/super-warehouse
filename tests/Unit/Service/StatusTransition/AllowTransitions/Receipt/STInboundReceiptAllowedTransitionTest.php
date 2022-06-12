<?php

namespace App\Tests\Unit\Service\StatusTransition\AllowTransitions\Receipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\Service\StatusTransition\AllowTransitions\Receipt\STInboundReceiptAllowedTransition;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class STInboundReceiptAllowedTransitionTest extends MockeryTestCase
{
    private STInboundReceiptAllowedTransition|null $receiptAllowedTransition;

    protected function setUp(): void
    {
        parent::setUp();
        $this->receiptAllowedTransition = new STInboundReceiptAllowedTransition();
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

        self::assertEquals(ReceiptStatusDictionary::APPROVED, $result->getDefault());
        self::assertEquals([ReceiptStatusDictionary::READY_TO_STOW], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::APPROVED));
        self::assertEquals([ReceiptStatusDictionary::STOWING], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::READY_TO_STOW));
        self::assertEquals([ReceiptStatusDictionary::DONE], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::STOWING));
        self::assertEquals([], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::DONE));
    }
}
