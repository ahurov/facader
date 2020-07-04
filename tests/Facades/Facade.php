<?php

/**
 * @noinspection PhpUnhandledExceptionInspection
 *
 * This file is auto-generated.
 */

namespace Facades;

use App;
use StdClass;
use StdClass as StdClass1;
use StdClass as StdClass2;
use Tests\Builder;
use Tests\Builder as Builder1;
use Tests\Builder as Builder2;
use Tests\Factory;
use Tests\Factory as Factory1;
use Tests\SecondaryService;
use Tests\Service;
use Tests\Service as Service1;
use Tests\ServiceInterface;

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
    public static function build(): StdClass
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
    public static function newObject(): StdClass
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
    public static function getPropertyFrom(StdClass $stdClass)
    {
        return static::getService()->getPropertyFrom($stdClass);
    }

    /**
     * @return Builder
     */
    public static function getBuilder()
    {
        return App::get(Builder::class);
    }

    /**
     * @return Factory
     */
    public static function getFactory()
    {
        return App::get(Factory::class);
    }

    /**
     * @return ServiceInterface
     */
    public static function getService()
    {
        return App::get(ServiceInterface::class);
    }
}
