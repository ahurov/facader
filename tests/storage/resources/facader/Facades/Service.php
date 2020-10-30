<?php

/**
 * This file is auto-generated.
 *
 * * @noinspection PhpUnhandledExceptionInspection
 * * @noinspection PhpDocMissingThrowsInspection
 */

namespace Facades;

use Gzhegow\Di\Di;
use Gzhegow\Facader\Tests\Services\SecondaryService;
use Gzhegow\Facader\Tests\Services\Service as _Service;

class Service
{
    /**
     * @return SecondaryService
     */
    public static function getSecondaryService()
    {
        return static::getService()->getSecondaryService();
    }

    /**
     * @param \StdClass $stdClass
     *
     * @return string
     */
    public static function getPropertyFrom(\StdClass $stdClass)
    {
        return static::getService()->getPropertyFrom($stdClass);
    }

    /**
     * @return _Service
     */
    public static function doActionThis()
    {
        return static::getService()->doActionThis();
    }

    /**
     * @return _Service
     */
    public static function doActionSelf()
    {
        return static::getService()->doActionSelf();
    }

    /**
     * @return _Service
     */
    public static function doActionStatic()
    {
        return static::getService()->doActionStatic();
    }

    /**
     * @return _Service
     */
    public static function doActionClassName()
    {
        return static::getService()->doActionClassName();
    }

    /**
     * @return _Service
     */
    public static function doActionInterfaceName()
    {
        return static::getService()->doActionInterfaceName();
    }

    /**
     * @return mixed|_Service
     */
    public static function doActionBeginsClassName()
    {
        return static::getService()->doActionBeginsClassName();
    }

    /**
     * @return _Service|mixed
     */
    public static function doActionEndsInterfaceName()
    {
        return static::getService()->doActionEndsInterfaceName();
    }

    /**
     * @return _Service
     */
    public static function getService(): _Service
    {
        return Di::getInstance()->get(_Service::class);
    }
}
