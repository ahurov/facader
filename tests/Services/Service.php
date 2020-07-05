<?php

namespace Tests\Services;

/**
 * Class Service
 */
class Service implements ServiceInterface
{
	/**
	 * @var Factory
	 */
	protected $factory;


	/**
	 * Constructor
	 *
	 * @param Factory $factory
	 */
	public function __construct(Factory $factory)
	{
		$this->factory = $factory;
	}


	/**
	 * @return SecondaryService
	 */
	public function getSecondaryService()
	{
		return new SecondaryService();
	}


	/**
	 * @param \StdClass $stdClass
	 *
	 * @return string
	 */
	public function getPropertyFrom(\StdClass $stdClass)
	{
		return (string) $stdClass->option;
	}


	/**
	 * @return $this
	 */
	public function doActionThis()
	{
		return $this;
	}

	/**
	 * @return self
	 */
	public function doActionSelf()
	{
		return $this;
	}

	/**
	 * @return static
	 */
	public function doActionStatic()
	{
		return $this;
	}

	/**
	 * @return Service
	 */
	public function doActionClassName()
	{
		return $this;
	}

	/**
	 * @return Service
	 */
	public function doActionInterfaceName()
	{
		return $this;
	}

	/**
	 * @return Service|mixed
	 */
	public function doActionBeginsClassName()
	{
		return $this;
	}

	/**
	 * @return mixed|Service
	 */
	public function doActionEndsInterfaceName()
	{
		return $this;
	}
}