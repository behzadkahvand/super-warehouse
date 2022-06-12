<?php

namespace App\Tests\Unit\Service\Utils;

use App\Dictionary\WebsiteAreaDictionary;
use App\Service\Utils\WebsiteAreaService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class WebsiteAreaServiceTest extends MockeryTestCase
{
    /**
     * @var \Mockery\LegacyMockInterface|\Mockery\MockInterface|RequestStack
     */
    protected $requestStackMock;

    /**
     * @var Mockery\LegacyMockInterface|Mockery\MockInterface|Request
     */
    protected $requestMock;

    /**
     * @var Mockery\LegacyMockInterface|Mockery\MockInterface|ParameterBag
     */
    protected $attributeMock;

    protected WebsiteAreaService $websiteAreaService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestStackMock = Mockery::mock(RequestStack::class);
        $this->requestMock      = Mockery::mock(Request::class);
        $this->attributeMock    = Mockery::mock(ParameterBag::class);

        $this->requestMock->attributes = $this->attributeMock;

        $this->websiteAreaService = new WebsiteAreaService($this->requestStackMock);
    }

    protected function tearDown(): void
    {
        unset($this->websiteAreaService);

        $this->requestStackMock = null;
        $this->requestMock = null;
        $this->attributeMock = null;
    }

    public function testItReturnNullIfIsNotInHttpRequestContext()
    {
        $this->requestStackMock->shouldReceive('getCurrentRequest')
                               ->once()
                               ->withNoArgs()
                               ->andReturnNull();

        self::assertNull($this->websiteAreaService->getArea());
    }

    public function testItCanGetWebsiteArea()
    {
        $this->requestStackMock->shouldReceive('getCurrentRequest')
                               ->once()
                               ->withNoArgs()
                               ->andReturn($this->requestMock);

        $this->attributeMock->shouldReceive('get')
                            ->once()
                            ->with('website_area')
                            ->andReturn(WebsiteAreaDictionary::AREA_SELLER);

        $result = $this->websiteAreaService->getArea();

        self::assertEquals(WebsiteAreaDictionary::AREA_SELLER, $result);
    }

    public function testItCanNotGetWebsiteArea()
    {
        $this->requestStackMock->shouldReceive('getCurrentRequest')
                               ->once()
                               ->withNoArgs()
                               ->andReturn($this->requestMock);

        $this->attributeMock->shouldReceive('get')
                            ->once()
                            ->with('website_area')
                            ->andReturnNull();

        self::assertNull($this->websiteAreaService->getArea());
    }

    public function testItCanCheckWebsiteAreaWhenWebsiteAreaIsInvalid()
    {
        self::assertFalse($this->websiteAreaService->isArea('invalid'));
    }

    public function testItCanCheckWebsiteAreaWhenWebsiteAreaIsSeller()
    {
        $this->requestStackMock->shouldReceive('getCurrentRequest')
                               ->once()
                               ->withNoArgs()
                               ->andReturn($this->requestMock);

        $this->attributeMock->shouldReceive('get')
                            ->once()
                            ->with('website_area')
                            ->andReturn(WebsiteAreaDictionary::AREA_SELLER);

        self::assertTrue($this->websiteAreaService->isArea(WebsiteAreaDictionary::AREA_SELLER));
    }

    public function testItCanCheckWebsiteAreaWhenWebsiteAreaIsNotSeller()
    {
        $this->requestStackMock->shouldReceive('getCurrentRequest')
                               ->once()
                               ->withNoArgs()
                               ->andReturn($this->requestMock);

        $this->attributeMock->shouldReceive('get')
                            ->once()
                            ->with('website_area')
                            ->andReturn(WebsiteAreaDictionary::AREA_CUSTOMER);

        self::assertFalse($this->websiteAreaService->isArea(WebsiteAreaDictionary::AREA_SELLER));
    }

    public function testItCanCheckRequestIsMadeInAdminArea()
    {
        $this->requestStackMock->shouldReceive('getCurrentRequest')
                               ->once()
                               ->withNoArgs()
                               ->andReturn($this->requestMock);

        $this->attributeMock->shouldReceive('get')
                            ->once()
                            ->with('website_area')
                            ->andReturn(WebsiteAreaDictionary::AREA_ADMIN);

        self::assertTrue($this->websiteAreaService->isAdminArea());
    }

    public function testItCanCheckRequestIsMadeInSellerArea()
    {
        $this->requestStackMock->shouldReceive('getCurrentRequest')
                               ->once()
                               ->withNoArgs()
                               ->andReturn($this->requestMock);

        $this->attributeMock->shouldReceive('get')
                            ->once()
                            ->with('website_area')
                            ->andReturn(WebsiteAreaDictionary::AREA_SELLER);

        self::assertTrue($this->websiteAreaService->isSellerArea());
    }

    public function testItCanCheckRequestIsMadeInCustomerArea()
    {
        $this->requestStackMock->shouldReceive('getCurrentRequest')
                               ->once()
                               ->withNoArgs()
                               ->andReturn($this->requestMock);

        $this->attributeMock->shouldReceive('get')
                            ->once()
                            ->with('website_area')
                            ->andReturn(WebsiteAreaDictionary::AREA_CUSTOMER);

        self::assertTrue($this->websiteAreaService->isCustomerArea());
    }
}
