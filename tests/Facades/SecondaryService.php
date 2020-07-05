<?php

/**
 * This file is auto-generated.
 *
 * * @noinspection PhpUnhandledExceptionInspection
 * * @noinspection PhpDocMissingThrowsInspection
 */

namespace Facades;

use Gzhegow\Di\Di;
use Tests\Services\SecondaryServiceInterface;

class SecondaryService
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
    public static function getSecondaryService()
    {
        return Di::makeOrFail(SecondaryServiceInterface::class);
    }
}
