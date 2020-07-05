<?php

/**
 * This file is auto-generated.
 *
 * @noinspection PhpUnhandledExceptionInspection
 */

namespace Facades;

use Gzhegow\Di\Di;
use Tests\Services\Builder;
use Tests\Services\Factory;
use Tests\Services\SecondaryService;
use Tests\Services\ServiceInterface;

class Facade
{
    /**
     * @param mixed $option
     */
    public static function setOption($option): void
    {
        static::getBuilder()->setOption($option);
    }

    /**
     * @return \StdClass
     */
    public static function build(): \StdClass
    {
        return static::getBuilder()->build();
    }

    /**
     * @return Builder
     */
    public static function newBuilder(): Builder
    {
        return static::getFactory()->newBuilder();
    }

    /**
     * @return \StdClass
     */
    public static function newObject(): \StdClass
    {
        return static::getFactory()->newObject();
    }

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
     * @return Builder
     */
    public static function getBuilder()
    {
        return Di::makeOrFail(Builder::class);
    }

    /**
     * @return Factory
     */
    public static function getFactory()
    {
        return Di::makeOrFail(Factory::class);
    }

    /**
     * @return ServiceInterface
     */
    public static function getService()
    {
        return Di::makeOrFail(ServiceInterface::class);
    }
}
