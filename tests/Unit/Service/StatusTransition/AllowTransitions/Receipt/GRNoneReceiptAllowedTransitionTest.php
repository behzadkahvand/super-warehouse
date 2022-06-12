<?php

namespace App\Tests\Unit\Service\StatusTransition\AllowTransitions\Receipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\Service\StatusTransition\AllowTransitions\Receipt\GRMarketPlacePackageReceiptAllowedTransition;
use App\Service\StatusTransition\AllowTransitions\Receipt\GRNoneReceiptAllowedTransition;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class GRNoneReceiptAllowedTransitionTest extends MockeryTestCase
{
    private GRNoneReceiptAllowedTransition|null $receiptAllowedTransition;

    protected function setUp(): void
    {
        parent::setUp();
        $this->receiptAllowedTransition = new GRNoneReceiptAllowedTransition();
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
        self::assertEquals([ReceiptStatusDictionary::BATCH_PROCESSING], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::APPROVED));
        self::assertEquals([ReceiptStatusDictionary::LABEL_PRINTING], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::BATCH_PROCESSING));
        self::assertEquals([ReceiptStatusDictionary::READY_TO_STOW], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::LABEL_PRINTING));
        self::assertEquals([ReceiptStatusDictionary::STOWING], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::READY_TO_STOW));
        self::assertEquals([ReceiptStatusDictionary::DONE], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::STOWING));
        self::assertEquals([], $result->getAllowedTransitionsFor(ReceiptStatusDictionary::DONE));
    }
}
