<?php

/**
 * This file is auto-generated.
 *
 * * @noinspection PhpUnhandledExceptionInspection
 * * @noinspection PhpDocMissingThrowsInspection
 */

namespace Facades;

use Gzhegow\Di\Di;
use Gzhegow\Facader\Tests\Services\SecondaryServiceInterface;

class SecondService
{
    /**
     * @param \StdClass $stdClass
     *
     * @return string
     */
    public static function getPropertyFrom(\StdClass $stdClass)
    {
        return static::getSecondaryService()->getPropertyFrom($stdClass);
    }

    /**
     * @return SecondaryServiceInterface
     */
    public static function getSecondaryService(): SecondaryServiceInterface
    {
        return Di::getInstance()->get(SecondaryServiceInterface::class);
    }
}
