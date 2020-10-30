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
use Gzhegow\Facader\Tests\Services\SecondaryServiceInterface;
use Gzhegow\Facader\Tests\Services\Service;

class ServiceFacade
{
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
     * @return SecondaryServiceInterface
     */
    public static function getSecondaryService()
    {
        return Di::getInstance()->get(SecondaryServiceInterface::class);
    }

    /**
     * @return Service
     */
    public static function doActionThis()
    {
        return static::getService()->doActionThis();
    }

    /**
     * @return Service
     */
    public static function doActionSelf()
    {
        return static::getService()->doActionSelf();
    }

    /**
     * @return Service
     */
    public static function doActionStatic()
    {
        return static::getService()->doActionStatic();
    }

    /**
     * @return Service
     */
    public static function doActionClassName()
    {
        return static::getService()->doActionClassName();
    }

    /**
     * @return Service
     */
    public static function doActionInterfaceName()
    {
        return static::getService()->doActionInterfaceName();
    }

    /**
     * @return mixed|Service
     */
    public static function doActionBeginsClassName()
    {
        return static::getService()->doActionBeginsClassName();
    }

    /**
     * @return Service|mixed
     */
    public static function doActionEndsInterfaceName()
    {
        return static::getService()->doActionEndsInterfaceName();
    }

    /**
     * @return Service
     */
    public static function getService()
    {
        return Di::getInstance()->get(Service::class);
    }
}
