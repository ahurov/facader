<?php

namespace Tests;

use Gzhegow\Di\Di;
use Tests\Services\Builder;
use Tests\Services\Factory;
use Tests\Services\Service;
use Gzhegow\Facader\Facader;
use PHPUnit\Framework\TestCase;
use Tests\Services\ServiceInterface;
use Tests\Services\SecondaryService;
use Tests\Services\SecondaryServiceInterface;

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

		$facader->generate(Di::class, 'makeOrFail', $generate);

		$this->assertFileExists($outputDir . DIRECTORY_SEPARATOR . 'Facades' . DIRECTORY_SEPARATOR . 'Facade.php');
	}

	public function test2()
	{
		$facader = new Facader();

		$facader->setFacadesRootPath($outputDir = __DIR__);

		$generate = [
			'Facades\Service'          => [
				Service::class,
			],
			'Facades\ServiceInterface' => [
				ServiceInterface::class => Service::class,
			],
			'Facades\SecondaryService' => [
				SecondaryServiceInterface::class => SecondaryService::class,
			],
		];

		$facader->generate(Di::class, 'makeOrFail', $generate);

		$this->assertFileExists($outputDir . DIRECTORY_SEPARATOR . 'Facades' . DIRECTORY_SEPARATOR . 'Facade.php');
	}
}