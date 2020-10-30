<?php

/**
 * This file is auto-generated.
 *
 * * @noinspection PhpUnhandledExceptionInspection
 * * @noinspection PhpDocMissingThrowsInspection
 */

use Gzhegow\Facader\Tests\Services\SecondaryService;
use Gzhegow\Facader\Tests\Services\SecondaryServiceInterface;
use Gzhegow\Facader\Tests\Services\Service;

class ThirdService
{
    /**
     * * @var Service service
     */
    protected $service;

    /**
     * * @var SecondaryServiceInterface secondaryService
     */
    protected $secondaryService;

    public function __construct(Service $service, SecondaryServiceInterface $secondaryService)
    {
        $this->service = $service;
        $this->secondaryService = $secondaryService;
    }

    /**
     * @return SecondaryService
     */
    public function getSecondaryService()
    {
        return $this->service->getSecondaryService();
    }

    /**
     * @param \StdClass $stdClass
     *
     * @return string
     */
    public function getPropertyFrom(StdClass $stdClass)
    {
        return $this->secondaryService->getPropertyFrom($stdClass);
    }

    /**
     * @return Service
     */
    public function doActionThis()
    {
        return $this->service->doActionThis();
    }

    /**
     * @return Service
     */
    public function doActionSelf()
    {
        return $this->service->doActionSelf();
    }

    /**
     * @return Service
     */
    public function doActionStatic()
    {
        return $this->service->doActionStatic();
    }

    /**
     * @return Service
     */
    public function doActionClassName()
    {
        return $this->service->doActionClassName();
    }

    /**
     * @return Service
     */
    public function doActionInterfaceName()
    {
        return $this->service->doActionInterfaceName();
    }

    /**
     * @return mixed|Service
     */
    public function doActionBeginsClassName()
    {
        return $this->service->doActionBeginsClassName();
    }

    /**
     * @return Service|mixed
     */
    public function doActionEndsInterfaceName()
    {
        return $this->service->doActionEndsInterfaceName();
    }
}
