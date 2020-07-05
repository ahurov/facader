<?php

/**
 * This file is auto-generated.
 *
 * @noinspection PhpUnhandledExceptionInspection
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
    public static function getService()
    {
        return Di::makeOrFail(_ServiceInterface::class);
    }
}
