<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\ActivePullListToLocate;

use App\Dictionary\PullListSortedPriorityDictionary;
use App\Entity\Admin;
use App\Entity\PullList;
use App\Repository\PullListRepository;
use App\Service\PullList\HandHeld\ActivePullListToLocate\ActivePullListToLocateService;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class ActivePullListToLocateServiceTest extends BaseUnitTestCase
{
    protected PullList|LegacyMockInterface|MockInterface|null $pullListMock;

    protected Admin|LegacyMockInterface|MockInterface|null $adminMock;

    protected LegacyMockInterface|MockInterface|PullListRepository|null $pullListRepoMock;

    protected ?ActivePullListToLocateService $activePullListToLocate;

    protected function setUp(): void
    {
        $this->pullListMock     = Mockery::mock(PullList::class);
        $this->adminMock        = Mockery::mock(Admin::class);
        $this->pullListRepoMock = Mockery::mock(PullListRepository::class);

        $this->activePullListToLocate = new ActivePullListToLocateService($this->pullListRepoMock);

        parent::setUp();
    }

    public function testItCanNotGetActivePullListToLocateWhenLocatorHasLatestActivePullList(): void
    {
        $this->pullListRepoMock->expects('latestActivePullListCount')
                               ->with($this->adminMock)
                               ->andReturns(1);

        $result = $this->activePullListToLocate->get($this->adminMock);

        self::assertNull($result);
    }

    public function testItCanNotGetActivePullListToLocateWhenPullListNotFound(): void
    {
        $this->pullListRepoMock->expects('latestActivePullListCount')
                               ->with($this->adminMock)
                               ->andReturns(0);
        $this->pullListRepoMock->expects('activePullListToLocate')
                               ->with($this->adminMock)
                               ->andReturnNull();

        $result = $this->activePullListToLocate->get($this->adminMock);

        self::assertNull($result);
    }

    public function testItCanGetActivePullListToLocate(): void
    {
        $this->pullListRepoMock->expects('latestActivePullListCount')
                               ->with($this->adminMock)
                               ->andReturns(0);
        $this->pullListRepoMock->expects('activePullListToLocate')
                               ->with($this->adminMock)
                               ->andReturns($this->pullListMock);

        $result = $this->activePullListToLocate->get($this->adminMock);

        self::assertEquals($this->pullListMock, $result);
    }
}
