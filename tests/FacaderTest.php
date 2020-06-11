<?php

namespace Tests;

use Gzhegow\Facader\Facader;
use PHPUnit\Framework\TestCase;

class FacaderTest extends TestCase
{
	public function test1()
	{
		$facader = new Facader();

		$facader->setFacadesRootPath($outputDir = __DIR__);

		$generate = [
			'Facades\Facade' => [
				Builder::class,
				Factory::class,
				ServiceInterface::class => Service::class,
			],
		];

		$facader->generate('App', 'get', $generate);

		$this->assertFileExists($outputDir . DIRECTORY_SEPARATOR . 'Facades' . DIRECTORY_SEPARATOR . 'Facade.php');
	}
}