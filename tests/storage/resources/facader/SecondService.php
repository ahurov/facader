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
    /**
     * * @var SecondaryServiceInterface secondaryService
     */
    protected $secondaryService;

    public function __construct(SecondaryServiceInterface $secondaryService)
    {
        $this->secondaryService = $secondaryService;
    }

    /**
     * @param \StdClass $stdClass
     *
     * @return string
     */
    public function getPropertyFrom(StdClass $stdClass)
    {
        return $this->secondaryService->getPropertyFrom($stdClass);
    }
}
