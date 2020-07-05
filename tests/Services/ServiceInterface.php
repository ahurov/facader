<?php

namespace Tests\Services;

/**
 * Class ServiceInterface
 */
interface ServiceInterface
{
	/**
	 * @param \StdClass $stdClass
	 *
	 * @return string
	 */
	public function getPropertyFrom(\StdClass $stdClass);
}