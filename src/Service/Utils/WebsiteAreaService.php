<?php

namespace App\Service\Utils;

use App\Dictionary\WebsiteAreaDictionary;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class WebsiteAreaService
 */
class WebsiteAreaService
{
    protected RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return string|null
     */
    public function getArea(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return null;
        }

        return $request->attributes->get('website_area');
    }

    /**
     * @param string $area
     *
     * @return bool
     */
    public function isArea(string $area): bool
    {
        return WebsiteAreaDictionary::isValid($area) ? $area === $this->getArea() : false;
    }

    /**
     * @return bool
     */
    public function isAdminArea(): bool
    {
        return $this->isArea(WebsiteAreaDictionary::AREA_ADMIN);
    }

    /**
     * @return bool
     */
    public function isCustomerArea(): bool
    {
        return $this->isArea(WebsiteAreaDictionary::AREA_CUSTOMER);
    }

    /**
     * @return bool
     */
    public function isSellerArea(): bool
    {
        return $this->isArea(WebsiteAreaDictionary::AREA_SELLER);
    }
}
