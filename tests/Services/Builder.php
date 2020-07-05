<?php

namespace Tests\Services;

/**
 * Class Builder
 */
class Builder
{
	/**
	 * @var
	 */
	protected $option;


	/**
	 * @param mixed $option
	 */
	public function setOption($option) : void
	{
		$this->option = $option;
	}


	/**
	 * @return \StdClass
	 */
	public function build() : \StdClass
	{
		$instance = new \StdClass();
		$instance->option = $this->option;

		return $instance;
	}
}