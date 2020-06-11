<?php

namespace Tests;


/**
 * Class Service
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