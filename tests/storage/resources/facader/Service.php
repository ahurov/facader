<?php

/**
 * This file is auto-generated.
 *
 * * @noinspection PhpUnhandledExceptionInspection
 * * @noinspection PhpDocMissingThrowsInspection
 */

use Gzhegow\Facader\Tests\Services\SecondaryService;
use Gzhegow\Facader\Tests\Services\Service as _Service;

class Service
{
    /**
     * * @var _Service service
     */
    protected $service;

    public function __construct(_Service $service)
    {
        $this->service = $service;
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
        return $this->service->getPropertyFrom($stdClass);
    }

    /**
     * @return _Service
     */
    public function doActionThis()
    {
        return $this->service->doActionThis();
    }

    /**
     * @return _Service
     */
    public function doActionSelf()
    {
        return $this->service->doActionSelf();
    }

    /**
     * @return _Service
     */
    public function doActionStatic()
    {
        return $this->service->doActionStatic();
    }

    /**
     * @return _Service
     */
    public function doActionClassName()
    {
        return $this->service->doActionClassName();
    }

    /**
     * @return _Service
     */
    public function doActionInterfaceName()
    {
        return $this->service->doActionInterfaceName();
    }

    /**
     * @return mixed|_Service
     */
    public function doActionBeginsClassName()
    {
        return $this->service->doActionBeginsClassName();
    }

    /**
     * @return _Service|mixed
     */
    public function doActionEndsInterfaceName()
    {
        return $this->service->doActionEndsInterfaceName();
    }
}
