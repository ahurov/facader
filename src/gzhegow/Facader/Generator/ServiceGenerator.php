<?php

namespace Gzhegow\Facader\Generator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

/**
 * Class ServiceGenerator
 */
class ServiceGenerator extends AbstractGenerator
{
	/**
	 * @param ClassType    $generatedClassType
	 * @param PhpNamespace $generatedNamespace
	 *
	 * @return static
	 */
	protected function duplicate(ClassType $generatedClassType, PhpNamespace $generatedNamespace = null)
	{
		$movingMethods = [];

		// add constructor
		$constructor = $generatedClassType->addMethod('__construct');
		$movingMethods[] = $constructor;

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
		$constructorParameters = [];
		$constructorBody = [];
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

			$propertyName = $this->str->camel($sourceClassName);
			$constructorParameter = $this->facader->newParameter($propertyName);
			$constructorParameter->setType($sourceUseAlias);

			$constructorParameters[] = $constructorParameter;
			$constructorBody[] = vsprintf('$this->%s = $%s;', [ $propertyName, $propertyName ]);
		}

		// set parameters to constructor
		$constructor->setParameters($constructorParameters);
		$constructor->setBody(implode(PHP_EOL, $constructorBody));

		// add new methods to facade class
		foreach ( $constructorParameters as $constructorParameter ) {
			$generatedClassType->addProperty($constructorParameter->getName())
				->addComment(vsprintf('* @var %s %s', [ $constructorParameter->getType(), $constructorParameter->getName() ]));
		}
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

		$sourcePropertyName = $this->str->camel($sourceClassName);

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
				. '$this->%s->%s(%s);',
				[
					$returnCmd,
					$sourcePropertyName,
					$method->getName(),
					implode(', ', $params),
				]
			));

			$resultMethods[] = $method;
		}

		return $resultMethods;
	}
}
