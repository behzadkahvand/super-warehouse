<?php

namespace App\Tests\Unit\Service\PickList;

use App\Entity\PickList;
use App\Service\PickList\PickListFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class PickListFactoryTest extends MockeryTestCase
{
    public function testCreate(): void
    {
        $pickListFactory = new PickListFactory();

        self::assertInstanceOf(PickList::class, $pickListFactory->create());
    }
}
