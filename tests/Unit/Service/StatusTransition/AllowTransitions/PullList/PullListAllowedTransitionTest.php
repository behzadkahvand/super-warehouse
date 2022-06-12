<?php

namespace App\Tests\Unit\Service\StatusTransition\AllowTransitions\PullList;

use App\Dictionary\PullListStatusDictionary;
use App\Service\StatusTransition\AllowTransitions\PullList\PullListAllowedTransition;
use App\Tests\Unit\BaseUnitTestCase;

class PullListAllowedTransitionTest extends BaseUnitTestCase
{
    protected PullListAllowedTransition|null $allowedTransition;

    protected function setUp(): void
    {
        parent::setUp();

        $this->allowedTransition = new PullListAllowedTransition();
    }

    public function testItCanCallInvoke(): void
    {
        $result = $this->allowedTransition->__invoke();

        self::assertEquals(PullListStatusDictionary::DRAFT, $result->getDefault());
        self::assertEquals(
            [PullListStatusDictionary::SENT_TO_LOCATOR],
            $result->getAllowedTransitionsFor(PullListStatusDictionary::DRAFT)
        );
        self::assertEquals(
            [PullListStatusDictionary::CONFIRMED_BY_LOCATOR],
            $result->getAllowedTransitionsFor(PullListStatusDictionary::SENT_TO_LOCATOR)
        );
        self::assertEquals(
            [PullListStatusDictionary::STOWING],
            $result->getAllowedTransitionsFor(PullListStatusDictionary::CONFIRMED_BY_LOCATOR)
        );
        self::assertEquals(
            [PullListStatusDictionary::CLOSED],
            $result->getAllowedTransitionsFor(PullListStatusDictionary::STOWING)
        );
        self::assertEquals([], $result->getAllowedTransitionsFor(PullListStatusDictionary::CLOSED));
    }
}
