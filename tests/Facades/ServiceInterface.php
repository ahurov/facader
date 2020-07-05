<?php

/**
 * This file is auto-generated.
 *
 * * @noinspection PhpUnhandledExceptionInspection
 * * @noinspection PhpDocMissingThrowsInspection
 */

namespace Facades;

use Gzhegow\Di\Di;
use Tests\Services\SecondaryService;
use Tests\Services\ServiceInterface as _ServiceInterface;

class ServiceInterface
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
     * @return _ServiceInterface
     */
    public static function doActionThis()
    {
        return static::getService()->doActionThis();
    }

    /**
     * @return _ServiceInterface
     */
    public static function doActionSelf()
    {
        return static::getService()->doActionSelf();
    }

    /**
     * @return _ServiceInterface
     */
    public static function doActionStatic()
    {
        return static::getService()->doActionStatic();
    }

    /**
     * @return _ServiceInterface
     */
    public static function doActionClassName()
    {
        return static::getService()->doActionClassName();
    }

    /**
     * @return _ServiceInterface
     */
    public static function doActionInterfaceName()
    {
        return static::getService()->doActionInterfaceName();
    }

    /**
     * @return mixed|_ServiceInterface
     */
    public static function doActionBeginsClassName()
    {
        return static::getService()->doActionBeginsClassName();
    }

    /**
     * @return _ServiceInterface|mixed
     */
    public static function doActionEndsInterfaceName()
    {
        return static::getService()->doActionEndsInterfaceName();
    }

    /**
     * @return _ServiceInterface
     */
    public static function getService()
    {
        return Di::makeOrFail(_ServiceInterface::class);
    }
}
