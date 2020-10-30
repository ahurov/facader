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
    /** * @var SecondaryServiceInterface econdaryErvice */
    public $econdaryErvice;

    /** * @var Service ervice */
    public $ervice;

    public function __construct(SecondaryServiceInterface $econdaryErvice, Service $ervice)
    {
        $this->econdaryErvice = $econdaryErvice;
        $this->ervice = $ervice;
    }

    /**
     * @param \StdClass $stdClass
     *
     * @return string
     */
    public function getPropertyFrom(StdClass $stdClass)
    {
        return $this->ervice->getPropertyFrom($stdClass);
    }

    /**
     * @return SecondaryService
     */
    public function getSecondaryService()
    {
        return $this->ervice->getSecondaryService();
    }

    /**
     * @return Service
     */
    public function doActionThis()
    {
        return $this->ervice->doActionThis();
    }

    /**
     * @return Service
     */
    public function doActionSelf()
    {
        return $this->ervice->doActionSelf();
    }

    /**
     * @return Service
     */
    public function doActionStatic()
    {
        return $this->ervice->doActionStatic();
    }

    /**
     * @return Service
     */
    public function doActionClassName()
    {
        return $this->ervice->doActionClassName();
    }

    /**
     * @return Service
     */
    public function doActionInterfaceName()
    {
        return $this->ervice->doActionInterfaceName();
    }

    /**
     * @return mixed|Service
     */
    public function doActionBeginsClassName()
    {
        return $this->ervice->doActionBeginsClassName();
    }

    /**
     * @return Service|mixed
     */
    public function doActionEndsInterfaceName()
    {
        return $this->ervice->doActionEndsInterfaceName();
    }
}
