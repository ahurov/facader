<?php

namespace Gzhegow\Facader\Tests\Di;

use Gzhegow\Facader\Facader;
use Gzhegow\Facader\FacaderInterface;
use Gzhegow\Facader\Di\FacaderProvider as GzhegowFacaderProvider;

class FacaderProvider extends GzhegowFacaderProvider
{
	public function register() : void
	{
		$this->di->bind(FacaderInterface::class, function () {
			$outputPath = $this->syncRealpath('resources');
			$config = require $this->syncRealpath('config');

			return $this->di->createOrFail(Facader::class, [
				'$outputPath' => $outputPath,
				'$config'     => $config,
			]);
		});
	}

	protected function sync() : array
	{
		return [
			'config'    => __DIR__ . '/../../config/facader.php',
			'resources' => __DIR__ . '/../../storage/resources/facader/',
		];
	}
}