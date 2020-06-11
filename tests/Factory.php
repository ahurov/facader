<?php

namespace Tests;

/**
 * Class Factory
 */
class Factory
{
	/**
	 * @return Builder
	 */
	public function newBuilder() : Builder
	{
		return new Builder();
	}


	/**
	 * @return \StdClass
	 */
	public function newObject() : \StdClass
	{
		return $this->newBuilder()->build();
	}
}