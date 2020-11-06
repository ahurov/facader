<?php

namespace Gzhegow\Facader\Di;

use Gzhegow\Facader\Facader;
use Gzhegow\Di\DeferableProvider;
use Gzhegow\Facader\FacaderInterface;

class FacaderProvider extends DeferableProvider
{
	public function register() : void
	{
		$this->di->bind(FacaderInterface::class, function () {
			$outputPath = $this->defineRealpath('resources');
			$config = require $this->defineRealpath('config');

			return $this->di->createOrFail(Facader::class, [
				'$outputPath' => $outputPath,
				'$config'     => $config,
			]);
		});
	}

	public function provides() : array
	{
		return [
			Facader::class,
			FacaderInterface::class,
		];
	}

	protected function define() : array
	{
		return [
			'config'    => __DIR__ . '/../../config/facader.php',
			'resources' => __DIR__ . '/../../storage/resources/facader/',
		];
	}
}