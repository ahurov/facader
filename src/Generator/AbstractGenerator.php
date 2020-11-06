<?php

namespace Gzhegow\Facader\Generator;

use Gzhegow\Support\Php;
use Gzhegow\Support\Str;
use Gzhegow\Support\Path;
use Gzhegow\Facader\Facader;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\ClassType;
use Gzhegow\Reflection\Reflection;
use Nette\PhpGenerator\PhpNamespace;
use Gzhegow\Facader\Exceptions\Logic\InvalidArgumentException;

/**
 * Class AbstractGenerator
 */
abstract class AbstractGenerator
{
	/**
	 * @var Path
	 */
	protected $path;
	/**
	 * @var Php
	 */
	protected $php;
	/**
	 * @var Str
	 */
	protected $str;
	/**
	 * @var Reflection
	 */
	protected $reflection;

	/**
	 * @var Facader
	 */
	protected $facader;

	/**
	 * @var array
	 */
	protected $uses = [];
	/**
	 * @var array
	 */
	protected $usesIndex = [];

	/**
	 * @var string
	 */
	protected $generatedClass;
	/**
	 * @var string
	 */
	protected $generatedClassNamespace;
	/**
	 * @var string
	 */
	protected $generatedClassName;
	/**
	 * @var array
	 */
	protected $sources;


	/**
	 * Constructor
	 *
	 * @param Path       $path
	 * @param Php        $php
	 * @param Str        $str
	 * @param Reflection $reflection
	 *
	 * @param Facader    $facader
	 * @param string     $generatedClass
	 * @param array      $sources
	 */
	public function __construct(
		Path $path,
		Php $php,
		Str $str,
		Reflection $reflection,

		Facader $facader,

		string $generatedClass,
		array $sources
	)
	{
		$this->path = $path;
		$this->php = $php;
		$this->str = $str;
		$this->reflection = $reflection;

		$this->facader = $facader;

		$this->sources = $sources;

		$this->setGeneratedClass($generatedClass);
	}


	/**
	 * @param string $generatedClass
	 *
	 * @return static
	 */
	public function setGeneratedClass(string $generatedClass)
	{
		if ('' === $generatedClass) {
			throw new InvalidArgumentException('GenerateClass should be not empty');
		}

		if (! $this->php->isValidClass($generatedClass)) {
			throw new InvalidArgumentException('GenerateClass is invalid: ' . $generatedClass, func_get_args());
		}

		$this->generatedClass = $generatedClass;
		[
			$this->generatedClassNamespace,
			$this->generatedClassName,
		] = $this->php->nsclass($generatedClass);

		return $this;
	}


	/**
	 * @return static
	 */
	public function generate()
	{
		// add facade class to index
		$this->putUseIndex($this->generatedClass);

		// create php file
		$file = $this->facader->newPhpFile();
		$file->addComment(implode(PHP_EOL, [
			'This file is auto-generated.',
			'',
			'* @noinspection PhpUnhandledExceptionInspection',
			'* @noinspection PhpDocMissingThrowsInspection',
		]));

		// create namespace
		$useCollector = $file;
		$generatedNamespace = null;
		if (isset($this->generatedClassNamespace)) {
			$generatedClassType = $this->facader->newClassType($this->generatedClassName);

			$generatedNamespace = $file->addNamespace($this->generatedClassNamespace);
			$generatedNamespace->add($generatedClassType);

			$useCollector = $generatedNamespace;

		} else {
			$generatedClassType = $file->addClass($this->generatedClassName);
		}

		// copy methods
		$this->duplicate($generatedClassType, $generatedNamespace);

		// add new uses
		foreach ( $this->uses as $use ) {
			$useCollector->addUse(...$use);
		}

		$subdir = $this->generatedClassNamespace
			? $this->path->normalize($this->generatedClassNamespace)
			: '';

		$dir = $this->path->join(
			$this->facader->getOutputPath(),
			$subdir
		);

		if (! is_dir($dir)) {
			mkdir($dir, 0755, true);

			$dir = realpath($dir);
		}

		// save file
		file_put_contents(
			$dir . DIRECTORY_SEPARATOR . $this->generatedClassName . '.php',
			$this->facader->newPsrPrinter()->printFile($file)
		);

		return $this;
	}


	/**
	 * @return static
	 */
	protected function resetUses()
	{
		$this->uses = [];
		$this->usesIndex = [
			'alias' => [],
		];

		return $this;
	}


	/**
	 * @param string $class
	 * @param null   $alias
	 *
	 * @return string
	 */
	protected function putUse(string $class, $alias = null) : string
	{
		if (! isset($alias)) {
			$alias = $this->php->class($class);
		}

		if (class_exists($class)) {
			if (! $rc = $this->reflection->reflectClass($class)->isUserDefined()) {
				return $alias;
			}
		}

		$this->uses[] = [ $class, $alias ];

		$alias = $this->putUseIndex($class, $alias);

		return $alias;
	}

	/**
	 * @param string $class
	 * @param null   $alias
	 *
	 * @return string
	 */
	protected function putUseIndex(string $class, $alias = null) : string
	{
		if (! isset($alias)) {
			$alias = $this->php->class($class);
		}

		$this->usesIndex[ 'alias' ][ $alias ] = $this->usesIndex[ 'alias' ][ $alias ] ?? 0;

		$i = $this->usesIndex[ 'alias' ][ $alias ]++;

		$result = $alias
			. ( $i
				?: '' );

		return $result;
	}


	/**
	 * @param mixed  $class
	 * @param Method $method
	 *
	 * @return array
	 */
	protected function fetchMethodParams($class, Method $method) : array
	{
		$result = [];

		foreach ( $method->getParameters() as $param ) {
			$rm = $this->reflection->reflectMethod($class, $method->getName());

			$isVariadic = false;
			foreach ( $rm->getParameters() as $rp ) {
				if ($rp->getName() !== $param->getName()) {
					continue;
				}

				$isVariadic = $rp->isVariadic();
			}

			$result[] = vsprintf('%s$%s', [
				$isVariadic
					? '...'
					: '',
				$param->getName(),
			]);

			// add use for method parameter dependencies
			$paramType = $param->getType();
			if ($paramType && class_exists($paramType)) {
				$this->putUse($paramType);
			}
		}

		return $result;
	}


	/**
	 * @param PhpNamespace $generatedNamespace
	 * @param ClassType    $generatedClassType
	 *
	 * @return static
	 */
	abstract protected function duplicate(ClassType $generatedClassType, PhpNamespace $generatedNamespace = null);
}
