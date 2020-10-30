<?php

namespace Gzhegow\Facader;

use Gzhegow\Support\Php;
use Gzhegow\Support\Str;
use Gzhegow\Support\Path;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PsrPrinter;
use Gzhegow\Reflection\Reflection;
use Nette\PhpGenerator\PhpNamespace;
use Gzhegow\Facader\Generator\FacadeGenerator;
use Gzhegow\Facader\Generator\ServiceGenerator;
use Gzhegow\Facader\Exceptions\RuntimeException;
use Gzhegow\Facader\Exceptions\Logic\InvalidArgumentException;

/**
 * Class Facader
 */
class Facader
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
	 * @var string
	 */
	protected $outputPath;

	/**
	 * @var array
	 */
	protected $servicesConfig = [];
	/**
	 * @var array
	 */
	protected $facadesConfig = [];


	/**
	 * Constructor
	 *
	 * @param Path       $path
	 * @param Php        $php
	 * @param Str        $str
	 * @param Reflection $reflection
	 *
	 * @param string     $outputPath
	 * @param array      $config
	 */
	public function __construct(
		Path $path,
		Php $php,
		Str $str,
		Reflection $reflection,

		string $outputPath,
		array $config = []
	)
	{
		$this->path = $path;
		$this->php = $php;
		$this->str = $str;
		$this->reflection = $reflection;

		$this->setOutputPath($outputPath);
		$this->setConfig($config);
	}


	/**
	 * @param string $containerClass
	 * @param string $facadeClass
	 * @param array  $sources
	 *
	 * @return FacadeGenerator
	 */
	public function newFacadeGenerator(string $containerClass, string $facadeClass, array $sources = []) : FacadeGenerator
	{
		return new FacadeGenerator(
			$this->path,
			$this->php,
			$this->str,
			$this->reflection,

			$this,

			$containerClass,
			$facadeClass,
			$sources
		);
	}

	/**
	 * @param string $serviceClass
	 * @param array  $sources
	 *
	 * @return ServiceGenerator
	 */
	public function newServiceGenerator(string $serviceClass, array $sources = []) : ServiceGenerator
	{
		return new ServiceGenerator(
			$this->path,
			$this->php,
			$this->str,
			$this->reflection,

			$this,

			$serviceClass,
			$sources
		);
	}


	/**
	 * @param string $class
	 *
	 * @return ClassType
	 */
	public function newClassTypeFrom(string $class) : ClassType
	{
		return ClassType::from($class);
	}


	/**
	 * @param string $namespace
	 *
	 * @return PhpNamespace
	 */
	public function newPhpNamespace(string $namespace) : PhpNamespace
	{
		return new PhpNamespace($namespace);
	}

	/**
	 * @param string      $name
	 *
	 * @param string|null $namespace
	 *
	 * @return ClassType
	 */
	public function newClassType(string $name, $namespace = null) : ClassType
	{
		$namespace = is_string($namespace)
			? $this->newPhpNamespace($namespace)
			: $namespace;

		return new ClassType($name, $namespace);
	}

	/**
	 * @param string $name
	 *
	 * @return Method
	 */
	public function newMethod(string $name) : Method
	{
		return new Method($name);
	}

	/**
	 * @param string $name
	 *
	 * @return Parameter
	 */
	public function newParameter(string $name) : Parameter
	{
		return new Parameter($name);
	}

	/**
	 * @return PhpFile
	 */
	public function newPhpFile() : PhpFile
	{
		return new PhpFile();
	}

	/**
	 * @return PsrPrinter
	 */
	public function newPsrPrinter() : PsrPrinter
	{
		return new PsrPrinter();
	}


	/**
	 * @return string
	 */
	public function getOutputPath() : string
	{
		return $this->outputPath;
	}


	/**
	 * @param string $outputPath
	 *
	 * @return Facader
	 */
	public function setOutputPath(string $outputPath)
	{
		if ('' === $outputPath) {
			throw new InvalidArgumentException('Path should be not empty');
		}

		if (false === ( $realpath = realpath($outputPath) )) {
			throw new RuntimeException('OutputPath directory not found: ' . $outputPath);
		}

		$this->outputPath = $realpath;

		return $this;
	}


	/**
	 * @param array $config
	 *
	 * @return Facader
	 */
	public function setConfig(array $config)
	{
		$this->servicesConfig = [];
		$this->facadesConfig = [];

		$this->mergeConfig($config);

		return $this;
	}

	/**
	 * @param array $config
	 *
	 * @return Facader
	 */
	public function mergeConfig(array $config)
	{
		$map = [];
		$map[ 'services' ] = 'servicesConfig';
		$map[ 'facades' ] = 'facadesConfig';

		foreach ( [ 'services', 'facades' ] as $type ) {
			$list = $config[ $type ] ?? [];

			foreach ( $list as $outputClass => $sources ) {
				foreach ( $sources as $interface => $class ) {
					if (! class_exists($class)) {
						throw new InvalidArgumentException('Invalid class: ' . $class);
					}

					if (is_string($interface)) {
						if (! interface_exists($interface)) {
							throw new InvalidArgumentException('Invalid interface: ' . $interface);
						}

						$this->{$map[ $type ]}[ $outputClass ][ $interface ] = $class;

					} else {
						$this->{$map[ $type ]}[ $outputClass ][] = $class;

					}
				}
			}
		}

		return $this;
	}


	/**
	 * @param string $containerClass
	 *
	 * @return Facader
	 */
	public function generate(string $containerClass)
	{
		$this->facades($containerClass, $this->facadesConfig);
		$this->services($this->servicesConfig);

		return $this;
	}


	/**
	 * @param string $serviceClass
	 * @param mixed  ...$sources
	 *
	 * @return Facader
	 */
	public function service(string $serviceClass, ...$sources)
	{
		[ $interfaces, $classes ] = $this->php->kwparams(...$sources);

		$this->newServiceGenerator($serviceClass, array_merge($classes, $interfaces))
			->generate();

		return $this;
	}

	/**
	 * @param mixed ...$sourcesByClassName
	 *
	 * @return Facader
	 */
	public function services(array $sourcesByClassName)
	{
		foreach ( $sourcesByClassName as $outputClass => $sources ) {
			$this->service($outputClass, $sources);
		}

		return $this;
	}


	/**
	 * @param string $containerClass
	 * @param string $facadeClass
	 * @param mixed  ...$sources
	 *
	 * @return $this
	 */
	public function facade(string $containerClass, string $facadeClass, ...$sources)
	{
		[ $interfaces, $classes ] = $this->php->kwparams(...$sources);

		$this->newFacadeGenerator($containerClass, $facadeClass, array_merge($classes, $interfaces))
			->generate();

		return $this;
	}

	/**
	 * @param string $containerClass
	 * @param mixed  ...$sourcesByClassName
	 *
	 * @return $this
	 */
	public function facades(string $containerClass, array $sourcesByClassName)
	{
		foreach ( $sourcesByClassName as $outputClass => $sources ) {
			$this->facade($containerClass, $outputClass, $sources);
		}

		return $this;
	}
}
