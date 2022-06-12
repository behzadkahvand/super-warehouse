<?php

namespace App\Tests\Unit\Service\PickList\HandHeld\Picking\Resolvers;

use App\Dictionary\PickListStatusDictionary;
use App\Entity\Admin;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Service\PickList\HandHeld\Exceptions\PickListNotPickableException;
use App\Service\PickList\HandHeld\Picking\Resolvers\CheckPickListIsPickAbleResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;
use Symfony\Component\Security\Core\Security;

final class CheckPickListIsPickAbleResolverTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|Mockery\MockInterface|Security|null $security;

    protected CheckPickListIsPickAbleResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->security = Mockery::mock(Security::class);

        $this->resolver = new CheckPickListIsPickAbleResolver($this->security);
    }

    public function testItCanResolveSuccess(): void
    {
        $itemSerial = Mockery::mock(ItemSerial::class);

        $admin = Mockery::mock(Admin::class);
        $admin->shouldReceive('getId')
              ->twice()
              ->withNoArgs()
              ->andReturn(1);

        $this->security->shouldReceive('getUser')
                         ->once()
                         ->withNoArgs()
                         ->andReturn($admin);

        $pickList = Mockery::mock(PickList::class);
        $pickList->shouldReceive('getPicker')
                 ->once()
                 ->withNoArgs()
                 ->andReturn($admin);
        $pickList->shouldReceive('getStatus')
                 ->once()
                 ->withNoArgs()
                 ->andReturn(PickListStatusDictionary::PICKING);

        $this->resolver->resolve($pickList, $itemSerial);
    }

    public function testResolveWhenException(): void
    {
        $itemSerial = Mockery::mock(ItemSerial::class);

        $admin = Mockery::mock(Admin::class);
        $admin->shouldReceive('getId')
              ->twice()
              ->withNoArgs()
              ->andReturn(1, 2);

        $this->security->shouldReceive('getUser')
                       ->once()
                       ->withNoArgs()
                       ->andReturn($admin);

        $pickList = Mockery::mock(PickList::class);
        $pickList->shouldReceive('getPicker')
                 ->once()
                 ->withNoArgs()
                 ->andReturn($admin);

        self::expectException(PickListNotPickableException::class);

        $this->resolver->resolve($pickList, $itemSerial);
    }

    public function testPriority(): void
    {
        self::assertEquals($this->resolver::getPriority(), 20);
    }
}
