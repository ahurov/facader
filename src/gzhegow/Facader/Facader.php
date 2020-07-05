<?php

namespace Gzhegow\Facader;

use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PsrPrinter;
use Nette\PhpGenerator\PhpNamespace;
use Gzhegow\Facader\Lib\Vendor\Zeronights\ExtendedReflectionClass;

/**
 * Class Facader
 */
class Facader
{
	/**
	 * @var string
	 */
	protected $facadesRootPath;

	/**
	 * @var array
	 */
	protected $uses = [];
	/**
	 * @var array
	 */
	protected $usesIndex = [];
	/**
	 * @var array
	 */
	protected $usesCounter = [];


	/**
	 * @return PsrPrinter
	 */
	protected function newPsrPrinter() : PsrPrinter
	{
		return new PsrPrinter();
	}

	/**
	 * @return PhpFile
	 */
	protected function newPhpFile() : PhpFile
	{
		return new PhpFile();
	}

	/**
	 * @param string $name
	 *
	 * @return PhpNamespace
	 */
	protected function newPhpNamespace(string $name) : PhpNamespace
	{
		return new PhpNamespace($name);
	}

	/**
	 * @param string $class
	 *
	 * @return ClassType
	 */
	protected function newClassTypeFrom(string $class) : ClassType
	{
		return ClassType::from($class);
	}

	/**
	 * @param string $name
	 *
	 * @return Method
	 */
	protected function newMethod(string $name) : Method
	{
		return new Method($name);
	}


	/**
	 * @return string
	 */
	public function getFacadesRootPath() : string
	{
		return $this->facadesRootPath;
	}

	/**
	 * @param string $facadesRootPath
	 */
	public function setFacadesRootPath(string $facadesRootPath) : void
	{
		$this->facadesRootPath = $facadesRootPath;
	}


	/**
	 * [
	 *    'MyNamespace\Facades\Obj' => Obj::class,
	 *
	 *    'MyNamespace\Facades\Obj' => [
	 *        Obj::class,
	 *    ],
	 *
	 *    'MyNamespace\Facades\Obj' => [
	 *        Obj::class,
	 *        Obj::class,
	 *    ],
	 *
	 *    'MyNamespace\Facades\Obj' => [
	 *        ObjInterface::class => Obj::class,
	 *    ],
	 *
	 *    'MyNamespace\Facades\Obj' => [
	 *        ObjInterface::class => Obj::class,
	 *        ObjInterface::class => Obj::class,
	 *    ],
	 * ];
	 *
	 * @param string $containerClass
	 * @param string $containerGetMethodName
	 * @param array  $config
	 *
	 * @return $this
	 */
	public function generate(
		string $containerClass,
		string $containerGetMethodName,
		array $config = []
	)
	{
		$containerClassName = $this->className($containerClass);

		foreach ( $config as $facadeClass => $from ) {
			$this->uses = [];
			$this->usesIndex = [];

			// import container
			$this->putUse($containerClass);

			// add facade class to index
			$this->putUseIndex($facadeClass);

			[
				'class'     => $facadeClassName,
				'namespace' => $facadeNamespace,
				'path'      => $facadeNamespacePath,
			] = $this->nsinfo($facadeClass);

			// create php file
			$file = $this->newPhpFile();
			$file->addComment(implode(PHP_EOL, [
				'This file is auto-generated.',
				'',
				'@noinspection PhpUnhandledExceptionInspection',
			]));

			// create namespace
			$namespace = $file->addNamespace($facadeNamespace);
			$namespace->add($facadeClassInstance = new ClassType($facadeClassName));

			// parse sources
			$facadeMethods = [];
			$fromArray = (array) $from;

			// merge methods
			foreach ( $fromArray as $idx => $fromClass ) {
				$fromClassInstance = $this->newClassTypeFrom($fromClass);

				// imports
				foreach ( $this->fetchUseStatements($fromClass) as $use ) {
					$this->putUse(...array_values($use));
				}

				// remove magic methods
				$fromClassInstance->removeMethod('__construct');
				$fromClassInstance->removeMethod('__destruct');
				$fromClassInstance->removeMethod('__call');
				$fromClassInstance->removeMethod('__callStatic');
				$fromClassInstance->removeMethod('__get');
				$fromClassInstance->removeMethod('__set');
				$fromClassInstance->removeMethod('__isset');
				$fromClassInstance->removeMethod('__unset');
				$fromClassInstance->removeMethod('__sleep');
				$fromClassInstance->removeMethod('__wakeup');
				$fromClassInstance->removeMethod('__toString');
				$fromClassInstance->removeMethod('__invoke');
				$fromClassInstance->removeMethod('__set_state');
				$fromClassInstance->removeMethod('__clone');
				$fromClassInstance->removeMethod('__debugInfo');

				// remove protected
				foreach ( $fromClassInstance->getMethods() as $method ) {
					if ($method->isPublic()) {
						continue;
					}

					$fromClassInstance->removeMethod($method->getName());
				}

				$facadeMethods = array_merge($facadeMethods,
					$this->fetchMethods($fromClass, $fromClassInstance)
				);
			}

			// copy methods
			foreach ( $fromArray as $idx => $fromClass ) {
				$fromClassName = $this->className($fromClass);

				$fromInterface = is_string($idx)
					? $idx
					: null;
				$fromInterfaceName = $fromInterface
					? $this->className($fromInterface)
					: null;

				$fromUse = $fromInterface ?? $fromClass;

				if ($fromInterface) {
					$fromAlias = null
						?? ( ( $facadeClassName === $fromInterfaceName )
							? '_' . $facadeClassName
							: null )
						?? $fromInterfaceName;

				} else {
					$fromAlias = null
						?? ( ( $facadeClassName === $fromClassName )
							? '_' . $facadeClassName
							: null )
						?? $fromClassName;

				}

				$this->putUse($fromUse, $fromAlias);

				// add facade accessor
				$facadeMethods[] = $method = ( new Method('get' . $fromClassName) )
					->setPublic()
					->setStatic()
					->addComment('@return ' . $fromAlias)
					->setBody(vsprintf('return %s::%s(%s::class);', [
						$containerClassName,
						$containerGetMethodName,
						$fromAlias,
					]));
			}

			// add new methods to facade class
			$facadeClassInstance->setMethods($facadeMethods);

			// add new uses
			foreach ( $this->uses as $use ) {
				$namespace->addUse(...$use);
			}

			$dir = $this->normalize($this->getFacadesRootPath()
				. DIRECTORY_SEPARATOR . $facadeNamespacePath
			);

			// create dir
			if (! $realpathDir = $this->is_dir($dir, false, true)) {
				mkdir($dir, 0755, true);

				$realpathDir = realpath($dir);
			}

			// save file
			file_put_contents(
				$realpathDir . DIRECTORY_SEPARATOR . $facadeClassName . '.php',
				$this->newPsrPrinter()->printFile($file)
			);
		}

		return $this;
	}


	/**
	 * @param string    $class
	 * @param ClassType $classType
	 *
	 * @return array
	 */
	protected function fetchMethods(string $class, ClassType $classType) : array
	{
		$result = [];

		$namespace = $this->namespace($class);
		$className = $this->className($class);

		foreach ( $classType->getMethods() as $method ) {
			// replace methods visibility to static
			$method->setStatic();

			// replace @return in PhpDoc
			$commentReturnType = null;
			if ($comment = $method->getComment()) {
				$content = array_map("trim", explode("\n", $comment));

				foreach ( $content as $idx => $line ) {
					if ($after = $this->substr_after($line, '@return')) {
						$after = trim($after);

						if (0
							|| ( $after === '$this' )
							|| ( $after === 'self' )
							|| ( $after === 'static' )
							|| ( $after === $className )
							|| ( $this->starts($after, $className . '|') )
							|| ( $this->ends($after, '|' . $className) )
						) {
							$content[ $idx ] = '@return ' . $className . '|static';
						} else {
							$commentReturnType = null
								?? ( class_exists($after)
									? $after
									: null )
								?? ( class_exists($after = $namespace . '\\' . $after)
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

			$params = $this->fetchMethodParams($class, $method);

			$method->setBody(
				vsprintf(''
					. '%s'
					. 'static::get%s()'
					. '->%s(%s);'
					, [
						$returnCmd,
						$className,
						$method->getName(),
						implode(', ', $params),
					])
			);

			$result[] = $method;
		}

		return $result;
	}

	/**
	 * @param string $class
	 * @param Method $method
	 *
	 * @return array
	 */
	protected function fetchMethodParams(string $class, Method $method) : array
	{
		$result = [];

		foreach ( $method->getParameters() as $param ) {
			$paramType = $param->getType();

			if ($paramType && class_exists($paramType)) {
				$this->putUse($paramType);
			}

			try {
				$rm = new \ReflectionMethod($class, $method->getName());
			}
			catch ( \ReflectionException $e ) {
				throw new \RuntimeException(null, null, $e);
			}

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
		}

		return $result;
	}


	/**
	 * @param string $class
	 *
	 * @return array
	 */
	protected function fetchUseStatements(string $class) : array
	{
		try {
			$rc = new ExtendedReflectionClass($class);
		}
		catch ( \ReflectionException $e ) {
			throw new \RuntimeException(null, null, $e);
		}

		$result = $rc->getUseStatements();

		return $result;
	}


	/**
	 * @param string $use
	 * @param null   $as
	 *
	 * @return string
	 */
	protected function putUse(string $use, $as = null) : string
	{
		if (! isset($as)) {
			$array = explode('\\', $use);

			$as = array_pop($array);
		}

		try {
			$rc = new \ReflectionClass($use);

			if (! $rc->isUserDefined()) {
				return $as;
			}
		}
		catch ( \ReflectionException $exception ) {
			throw new \RuntimeException(null, null, $exception);
		}

		$as = $this->putUseIndex($use, $as);

		$this->uses[] = [ $use, $as ];

		return $as;
	}

	/**
	 * @param string $use
	 * @param null   $as
	 *
	 * @return string
	 */
	protected function putUseIndex(string $use, $as = null) : string
	{
		if (! isset($as)) {
			$array = explode('\\', $use);

			$as = array_pop($array);
		}

		$existingUses = $this->usesIndex[ $as ] ?? null;

		if ($existingUses) {
			if (! isset($existingUses[ $use ])) {
				++$this->usesCounter[ $as ];
			}

		} else {
			$this->usesCounter[ $as ] = 0;

		}

		$this->usesIndex[ $as ][ $use ] = true;

		$i = $this->usesCounter[ $as ];

		$result = $as . ( $i
				? $i
				: '' );

		return $result;
	}


	/**
	 * @param mixed $needle
	 *
	 * @return array
	 */
	protected function nsinfo($needle) : array
	{
		if (! ( is_object($needle) || is_string($needle) )) {
			throw new \InvalidArgumentException();
		}

		if (is_object($needle)) {
			$class = get_class($needle);

		} elseif (is_string($needle)) {
			$class = $needle;

		} else {
			throw new \InvalidArgumentException();

		}

		$namespace = explode('\\', $class);

		$class = array_pop($namespace);
		$namespace = ltrim(implode('\\', $namespace), '\\');

		$path = $this->normalize($namespace);

		return [
			'path'      => $path,
			'class'     => $class,
			'namespace' => $namespace,
		];
	}


	/**
	 * @param mixed $needle
	 *
	 * @return string
	 */
	protected function className($needle) : string
	{
		return $this->nsinfo($needle)[ 'class' ];
	}

	/**
	 * @param mixed $needle
	 *
	 * @return string
	 */
	protected function namespace($needle) : string
	{
		return $this->nsinfo($needle)[ 'namespace' ];
	}

	/**
	 * @param mixed $needle
	 *
	 * @return string
	 */
	protected function namespacePath($needle) : string
	{
		return $this->nsinfo($needle)[ 'path' ];
	}


	/**
	 * @param string $path
	 *
	 * @return string
	 */
	protected function normalize(string $path = '') : string
	{
		return $this->optimize($path, DIRECTORY_SEPARATOR);
	}

	/**
	 * @param string $path
	 * @param string $separator
	 *
	 * @return mixed
	 */
	protected function optimize(string $path = '', string $separator = '/') : string
	{
		if ('' === $path) {
			return '';
		}

		if (false !== strpos($path, "\0")) {
			throw new \InvalidArgumentException('Invalid path passed: ' . $path);
		}

		$result = $path;
		$result = implode("\0", explode($separator, $result));
		$result = implode("\0", explode('/', $result));
		$result = implode("\0", explode('\\', $result));
		$result = implode("\0", explode(DIRECTORY_SEPARATOR, $result));

		$result = implode($separator, explode("\0", $result));

		return $result;
	}


	/**
	 * @param string $haystack
	 * @param string $needle
	 *
	 * @return null|string
	 */
	protected function starts(string $haystack, string $needle = '') : ?string
	{
		if ('' === $needle) return $haystack;
		if ('' === $haystack) return null;

		$result = ( 0 === mb_stripos($haystack, $needle) )
			? mb_substr($haystack, mb_strlen($needle))
			: null;

		return $result;
	}

	/**
	 * @param string $haystack
	 * @param string $needle
	 *
	 * @return string
	 */
	protected function ends(string $haystack, string $needle = '') : ?string
	{
		if ('' === $needle) return $haystack;
		if ('' === $haystack) return null;

		$result = ( ( $pos = ( mb_strlen($haystack) - mb_strlen($needle) ) ) === mb_strripos($haystack, $needle) )
			? mb_substr($haystack, 0, mb_strlen($haystack) - mb_strlen($needle))
			: null;

		return $result;
	}

	/**
	 * @param string $haystack
	 * @param string $needle
	 *
	 * @return null|string
	 */
	protected function substr_after(string $haystack, string $needle = '') : ?string
	{
		if ('' === $needle) return $haystack;

		$result = ( false !== ( $pos = mb_stripos($haystack, $needle) ) )
			? mb_substr($haystack, $pos + mb_strlen($needle))
			: null;

		return $result;
	}


	/**
	 * @param           $fileName
	 * @param bool|null $caseSensitive
	 * @param bool|null $multibyte
	 *
	 * @return string|null
	 */
	protected function is_dir($fileName, bool $caseSensitive = null, bool $multibyte = null) : ?string
	{
		$caseSensitive = $caseSensitive ?? true;
		$multibyte = $multibyte ?? false;

		if (is_dir($fileName)) {
			return realpath($fileName);
		}

		if ($caseSensitive) {
			return null;
		}

		$files = $this->glob($fileName, null, $multibyte);

		foreach ( $files as $file ) {
			if ($multibyte) {
				$lFile = mb_strtolower($file);
				$lFileName = mb_strtolower($fileName);

			} else {
				$lFile = strtolower($file);
				$lFileName = strtolower($fileName);

			}

			if ($lFile !== $lFileName) {
				continue;
			}

			if (! is_dir($file)) {
				return null;
			}

			return realpath($file);
		}

		return null;
	}


	/**
	 * @param string   $pattern
	 * @param int|null $flags
	 * @param string   $dir
	 * @param bool     $multibyte
	 *
	 * @return array|false
	 */
	protected function glob(string $pattern, int $flags = null, string $dir = null, bool $multibyte = false) : ?array
	{
		if ('' === $pattern) {
			throw new \InvalidArgumentException('Pattern should be not empty', func_get_args());
		}

		if ($dir && ( '' === $dir )) {
			throw new \InvalidArgumentException('Dir should be not empty');
		}

		$len = $multibyte
			? mb_strlen($pattern)
			: strlen($pattern);

		$p = '';
		for ( $i = 0; $i < $len; $i++ ) {
			if ($multibyte) {
				$u = mb_strtoupper($pattern[ $i ]);
				$l = mb_strtolower($pattern[ $i ]);

			} else {
				$u = strtoupper($pattern[ $i ]);
				$l = strtolower($pattern[ $i ]);

			}

			if ($u === $l) {
				$p .= $pattern[ $i ];

			} else {
				$p .= "[{$l}{$u}]";

			}
		}

		if ($dir) {
			$p = $dir . DIRECTORY_SEPARATOR . $p;
		}

		$files = glob($p, $flags);

		if (( $flags & GLOB_NOCHECK )
			&& ( ! is_array($files) )
		) {
			return $files;
		}

		if (( ! ( $flags & GLOB_NOSORT ) )
			&& is_array($files)
		) {
			usort($files, function ($a, $b) {
				return is_dir($a) - is_dir($b);
			});
		}

		return $files;
	}
}
