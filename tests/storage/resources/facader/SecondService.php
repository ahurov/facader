<?php

/**
 * This file is auto-generated.
 *
 * * @noinspection PhpUnhandledExceptionInspection
 * * @noinspection PhpDocMissingThrowsInspection
 */

use Gzhegow\Facader\Tests\Services\SecondaryServiceInterface;

class SecondService
{
    /** * @var SecondaryServiceInterface econdaryErvice */
    public $econdaryErvice;

    public function __construct(SecondaryServiceInterface $econdaryErvice)
    {
        $this->econdaryErvice = $econdaryErvice;
    }

    /**
     * @param \StdClass $stdClass
     *
     * @return string
     */
    public function getPropertyFrom(StdClass $stdClass)
    {
        return $this->econdaryErvice->getPropertyFrom($stdClass);
    }
}
