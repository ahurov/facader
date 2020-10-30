<?php

namespace Gzhegow\Facader\Tests\Services;

/**
 * Class SecondaryService
 */
class SecondaryService implements SecondaryServiceInterface
{
	/**
	 * @param \StdClass $stdClass
	 *
	 * @return string
	 */
	public function getPropertyFrom(\StdClass $stdClass)
	{
		return (string) $stdClass->option;
	}
}