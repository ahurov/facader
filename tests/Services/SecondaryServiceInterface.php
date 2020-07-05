<?php

namespace Tests\Services;

/**
 * Class SecondaryServiceInterface
 */
interface SecondaryServiceInterface
{
	/**
	 * @param \StdClass $stdClass
	 *
	 * @return string
	 */
	public function getPropertyFrom(\StdClass $stdClass);
}