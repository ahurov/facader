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
    /** * @var _Service ervice */
    public $ervice;

    public function __construct(_Service $ervice)
    {
        $this->ervice = $ervice;
    }

    /**
     * @return SecondaryService
     */
    public function getSecondaryService()
    {
        return $this->ervice->getSecondaryService();
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
     * @return _Service
     */
    public function doActionThis()
    {
        return $this->ervice->doActionThis();
    }

    /**
     * @return _Service
     */
    public function doActionSelf()
    {
        return $this->ervice->doActionSelf();
    }

    /**
     * @return _Service
     */
    public function doActionStatic()
    {
        return $this->ervice->doActionStatic();
    }

    /**
     * @return _Service
     */
    public function doActionClassName()
    {
        return $this->ervice->doActionClassName();
    }

    /**
     * @return _Service
     */
    public function doActionInterfaceName()
    {
        return $this->ervice->doActionInterfaceName();
    }

    /**
     * @return mixed|_Service
     */
    public function doActionBeginsClassName()
    {
        return $this->ervice->doActionBeginsClassName();
    }

    /**
     * @return _Service|mixed
     */
    public function doActionEndsInterfaceName()
    {
        return $this->ervice->doActionEndsInterfaceName();
    }
}
