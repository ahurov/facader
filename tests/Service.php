<?php

namespace Tests;

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
}