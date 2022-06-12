<?php

namespace App\Tests\Unit\Service\PullListItem;

use App\Entity\PullListItem;
use App\Service\PullListItem\PullListItemFactory;
use App\Tests\Unit\BaseUnitTestCase;

class PullListItemFactoryTest extends BaseUnitTestCase
{
    public function testItCanGetPullListItem(): void
    {
        $factory = new PullListItemFactory();

        self::assertInstanceOf(PullListItem::class, $factory->getPullListItem());
    }
}
