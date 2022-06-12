<?php

namespace App\Tests\Unit\Service\Seller;

use App\Entity\Seller;
use App\Repository\SellerRepository;
use App\Service\Seller\DTO\SellerData;
use App\Service\Seller\SellerFactory;
use App\Service\Seller\SellerUpsertService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class SellerUpsertServiceTest extends BaseUnitTestCase
{
    protected LegacyMockInterface|EntityManagerInterface|MockInterface|null $em;

    protected SellerRepository|LegacyMockInterface|MockInterface|null $sellerRepoMock;

    protected SellerFactory|LegacyMockInterface|MockInterface|null $factoryMock;

    protected Seller|LegacyMockInterface|MockInterface|null $sellerMock;

    protected SellerData|LegacyMockInterface|MockInterface|null $dataMock;

    protected ?SellerUpsertService $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->em             = Mockery::mock(EntityManagerInterface::class);
        $this->sellerRepoMock = Mockery::mock(SellerRepository::class);
        $this->factoryMock    = Mockery::mock(SellerFactory::class);
        $this->sellerMock     = Mockery::mock(Seller::class);
        $this->dataMock       = Mockery::mock(SellerData::class);

        $this->sut = new SellerUpsertService(
            $this->em,
            $this->sellerRepoMock,
            $this->factoryMock
        );
    }

    public function testItCanCreateSeller(): void
    {
        $this->factoryMock->expects('create')->withNoArgs()->andReturns($this->sellerMock);

        $this->dataMock->expects('getId')->withNoArgs()->andReturns(1);
        $this->dataMock->expects('getIdentifier')->withNoArgs()->andReturns('khfd1');
        $this->dataMock->expects('getName')->withNoArgs()->andReturns('test');
        $this->dataMock->expects('getMobile')->withNoArgs()->andReturns('09010001000');

        $this->sellerMock->expects('setId')->with(1)->andReturnSelf();
        $this->sellerMock->expects('setIdentifier')->with('khfd1')->andReturnSelf();
        $this->sellerMock->expects('setName')->with('test')->andReturnSelf();
        $this->sellerMock->expects('setMobile')->with('09010001000')->andReturnSelf();

        $this->em->expects('persist')->with($this->sellerMock)->andReturns();
        $this->em->expects('flush')->withNoArgs()->andReturns();

        $this->sut->create($this->dataMock);
    }

    public function testItCanNotUpdateSellerWhenSellerNotFound(): void
    {
        $this->dataMock->expects('getId')->withNoArgs()->andReturns(1);

        $this->sellerRepoMock->expects('find')->with(1)->andReturnNull();

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Seller not found!');

        $this->sut->update($this->dataMock);
    }

    public function testItCanUpdateSeller(): void
    {
        $this->dataMock->expects('getId')->withNoArgs()->andReturns(1);
        $this->dataMock->expects('getIdentifier')->withNoArgs()->andReturns('lkji5');
        $this->dataMock->expects('getName')->withNoArgs()->andReturns('test-sup');
        $this->dataMock->expects('getMobile')->withNoArgs()->andReturns('09010002000');

        $this->sellerMock->expects('setIdentifier')->with('lkji5')->andReturnSelf();
        $this->sellerMock->expects('setName')->with('test-sup')->andReturnSelf();
        $this->sellerMock->expects('setMobile')->with('09010002000')->andReturnSelf();

        $this->sellerRepoMock->expects('find')->with(1)->andReturns($this->sellerMock);

        $this->em->expects('flush')->withNoArgs()->andReturns();

        $this->sut->update($this->dataMock);
    }
}
