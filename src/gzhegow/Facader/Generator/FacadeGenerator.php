<?php

namespace Gzhegow\Facader\Generator;

use Gzhegow\Support\Php;
use Gzhegow\Support\Str;
use Gzhegow\Support\Path;
use Gzhegow\Facader\Facader;
use Nette\PhpGenerator\ClassType;
use Gzhegow\Reflection\Reflection;
use Nette\PhpGenerator\PhpNamespace;
use Psr\Container\ContainerInterface;
use Gzhegow\Facader\Exceptions\Logic\InvalidArgumentException;

/**
 * Class FacadeGenerator
 */
class FacadeGenerator extends AbstractGenerator
{
	/**
	 * @var string
	 */
	protected $containerClass;
	/**
	 * @var string
	 */
	protected $containerClassName;


	/**
	 * Constructor
	 *
	 * @param Path       $path
	 * @param Php        $php
	 * @param Str        $str
	 * @param Reflection $reflection
	 *
	 * @param Facader    $facader
	 *
	 * @param string     $containerClass
	 * @param string     $facadeClass
	 * @param array      $sources
	 */
	public function __construct(
		Path $path,
		Php $php,
		Str $str,
		Reflection $reflection,

		Facader $facader,

		string $containerClass,
		string $facadeClass,
		array $sources = []
	)
	{
		parent::__construct($path, $php, $str, $reflection, $facader, $facadeClass, $sources);

		$this->setContainerClass($containerClass);
	}


	/**
	 * @param string $containerClass
	 *
	 * @return FacadeGenerator
	 */
	public function setContainerClass(string $containerClass)
	{
		if ('' === $containerClass) {
			throw new InvalidArgumentException('ContainerClass should be not empty');
		}

		if (! is_a($containerClass, ContainerInterface::class, true)) {
			throw new InvalidArgumentException('ContainerClass should implements ' . ContainerInterface::class, func_get_args());
		}

		if (! method_exists($containerClass, 'getInstance')) {
			throw new InvalidArgumentException('ContainerClass should have method getInstance() to build static facades', func_get_args());
		}

		$this->containerClass = $containerClass;
		$this->containerClassName = $this->php->class($containerClass);

		return $this;
	}


	/**
	 * @return AbstractGenerator
	 */
	public function generate()
	{
		$this->putUse($this->containerClass);

		return parent::generate();
	}


	/**
	 * @param ClassType         $generatedClassType
	 * @param PhpNamespace|null $generatedNamespace
	 *
	 * @return static
	 */
	protected function duplicate(ClassType $generatedClassType, PhpNamespace $generatedNamespace = null)
	{
		$movingMethods = [];

		// merge methods
		foreach ( $this->sources as $sourceInterface => $sourceClass ) {
			$sourceInterface = is_string($sourceInterface)
				? $sourceInterface
				: null;

			$sourceClassType = $this->facader->newClassTypeFrom($sourceClass);

			// imports
			[ $useStatements ] = $this->reflection->reflectClass($sourceClass)->getUseStatements();
			foreach ( $useStatements as [ $class, $alias ] ) {
				$this->putUse($class, $alias);
			}

			// remove magic methods
			$sourceClassType->removeMethod('__construct');
			$sourceClassType->removeMethod('__destruct');
			$sourceClassType->removeMethod('__call');
			$sourceClassType->removeMethod('__callStatic');
			$sourceClassType->removeMethod('__get');
			$sourceClassType->removeMethod('__set');
			$sourceClassType->removeMethod('__isset');
			$sourceClassType->removeMethod('__unset');
			$sourceClassType->removeMethod('__sleep');
			$sourceClassType->removeMethod('__wakeup');
			$sourceClassType->removeMethod('__toString');
			$sourceClassType->removeMethod('__invoke');
			$sourceClassType->removeMethod('__set_state');
			$sourceClassType->removeMethod('__clone');
			$sourceClassType->removeMethod('__debugInfo');

			// only public
			foreach ( $sourceClassType->getMethods() as $method ) {
				if (! $method->isPublic()) {
					$sourceClassType->removeMethod($method->getName());
				}
			}

			$sourceMethods = $this->fetchMethods($sourceClassType, $sourceClass, $sourceInterface);

			$movingMethods = array_merge($movingMethods, $sourceMethods);
		}

		// add static accessors
		foreach ( $this->sources as $sourceInterface => $sourceClass ) {
			$sourceClassName = $this->php->class($sourceClass);

			$sourceInterfaceName = null;
			if (is_string($sourceInterface) && ( '' !== $sourceInterface )) {
				$sourceInterfaceName = $this->php->class($sourceInterface);
				$sourceUseClass = $sourceInterface;

			} else {
				$sourceUseClass = $sourceClass;
			}

			if ($sourceInterfaceName) {
				$sourceUseAlias = ( $this->generatedClassName === $sourceInterfaceName )
					? '_' . $sourceInterfaceName
					: $sourceInterfaceName;

			} else {
				$sourceUseAlias = ( $this->generatedClassName === $sourceClassName )
					? '_' . $sourceClassName
					: $sourceClassName;
			}

			$this->putUse($sourceUseClass, $sourceUseAlias);

			$facadeAccessor = ( $this->facader->newMethod('get' . $sourceClassName) )
				->setPublic()
				->setStatic()
				->addComment('@return ' . $sourceUseAlias)
				->setBody(vsprintf('return %s::getInstance()->get(%s::class);', [
					$this->containerClassName,
					$sourceUseAlias,
				]));

			$movingMethods[] = $facadeAccessor;
		}

		// add new methods to facade class
		$generatedClassType->setMethods($movingMethods);

		return $this;
	}


	/**
	 * @param ClassType   $sourceClassType
	 * @param string      $sourceClass
	 * @param string|null $sourceInterface
	 *
	 * @return array
	 */
	protected function fetchMethods(ClassType $sourceClassType, string $sourceClass, string $sourceInterface = null) : array
	{
		$resultMethods = [];

		[ $sourceNamespace, $sourceClassName ] = $this->php->nsclass($sourceClass);

		$sourceInterfaceName = isset($sourceInterface) && ( '' !== $sourceInterface )
			? $this->php->class($sourceInterface)
			: null;

		if ($sourceInterfaceName) {
			$sourceAlias = ( $this->generatedClassName === $sourceInterfaceName )
				? '_' . $sourceInterfaceName
				: $sourceInterfaceName;

		} else {
			$sourceAlias = ( $this->generatedClassName === $sourceClassName )
				? '_' . $sourceClassName
				: $sourceClassName;
		}

		foreach ( $sourceClassType->getMethods() as $method ) {
			// make method static because of static facade
			$method->setStatic();

			// replace @return in PhpDoc
			$commentReturnType = null;
			if ($comment = $method->getComment()) {
				$content = array_map("trim", explode("\n", $comment));

				foreach ( $content as $idx => $line ) {
					if (null !== ( $after = $this->str->starts($line, '@return') )) {
						$after = trim($after);

						$begins = null;
						$ends = null;
						if (0
							|| ( $after === '$this' )
							|| ( $after === 'self' )
							|| ( $after === 'static' )
							|| ( $after === $sourceClassName )
							|| ( $begins = $this->str->starts($after, $sourceClassName . '|') )
							|| ( $ends = $this->str->ends($after, '|' . $sourceClassName) )
						) {
							$content[ $idx ] = '@return '
								. implode('|', array_filter([
									$begins,
									$sourceAlias,
									$ends,
								]));

						} else {
							$commentReturnType = null
								?? ( class_exists($after)
									? $after
									: null )
								?? ( class_exists($after = $sourceNamespace . '\\' . $after)
									? $after
									: null );
						}
					}
				}

				$method->setComment(implode(PHP_EOL, $content));
			}

			// add use for return type
			$returnType = $method->getReturnType() ?? $commentReturnType;
			if ($returnType && class_exists($returnType)) {
				$this->putUse($returnType);
			}

			// replace return command into expected
			switch ( true ):
				case ( $returnType === 'void' ):
					$returnCmd = '';
					break;

				case ( $returnType === \Generator::class ):
				case ( is_a($returnType, \Generator::class) === 'void' ):
					$returnCmd = 'yield ';
					break;

				default:
					$returnCmd = 'return ';
					break;

			endswitch;

			// collect method params
			$params = $this->fetchMethodParams($sourceClass, $method);

			// replace body with new params and accessor
			$method->setBody(vsprintf(''
				. '%s'
				. 'static::get%s()->%s(%s);',
				[
					$returnCmd,
					$sourceClassName,
					$method->getName(),
					implode(', ', $params),
				]
			));

			$resultMethods[] = $method;
		}

		return $resultMethods;
	}
}
