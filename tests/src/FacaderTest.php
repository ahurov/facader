<?php

namespace Gzhegow\Facader\Tests;

use Gzhegow\Di\Di;
use Gzhegow\Facader\Facader;
use Gzhegow\Facader\FacaderInterface;
use Gzhegow\Facader\Tests\Services\Service;
use Gzhegow\Facader\Tests\Di\FacaderProvider;
use Gzhegow\Facader\Tests\Services\SecondaryService;
use Gzhegow\Facader\Tests\Services\SecondaryServiceInterface;

class FacaderTest extends AbstractTestCase
{
	protected function getFacader() : Facader
	{
		return static::$di->getOrFail(FacaderInterface::class);
	}

	protected function setUp() : void
	{
		parent::setUp();

		$facader = $this->getFacader();

		$outputPath = $facader->getOutputPath();

		// drop old
		$it = new \RecursiveDirectoryIterator($outputPath, \RecursiveDirectoryIterator::SKIP_DOTS);
		$iit = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
		foreach ( $iit as $file ) {
			$todo = ( $file->isDir()
				? 'rmdir'
				: 'unlink' );
			$todo($file->getRealPath());
		}
	}

	public function testGenerate_()
	{
		$facader = $this->getFacader();

		$outputPath = $facader->getOutputPath();

		$facader->generate(Di::class);

		$this->assertFileExists($outputPath . DIRECTORY_SEPARATOR . 'Service.php');
		$this->assertFileExists($outputPath . DIRECTORY_SEPARATOR . 'SecondService.php');
		$this->assertFileExists($outputPath . DIRECTORY_SEPARATOR . 'ThirdService.php');
		$this->assertFileExists($outputPath . DIRECTORY_SEPARATOR . 'Facades' . DIRECTORY_SEPARATOR . 'Service.php');
		$this->assertFileExists($outputPath . DIRECTORY_SEPARATOR . 'Facades' . DIRECTORY_SEPARATOR . 'SecondService.php');
		$this->assertFileExists($outputPath . DIRECTORY_SEPARATOR . 'Facades' . DIRECTORY_SEPARATOR . 'ServiceFacade.php');
	}

	public function testServices_()
	{
		$facader = $this->getFacader();

		$outputPath = $facader->getOutputPath();

		$facades = [
			'Facades\Service'       => [
				Service::class,
			],
			'Facades\SecondService' => [
				SecondaryServiceInterface::class => SecondaryService::class,
			],
			'Facades\ServiceFacade' => [
				Service::class,
				SecondaryServiceInterface::class => SecondaryService::class,
			],
		];

		$facader->facades(Di::class, $facades);

		$this->assertFileExists($outputPath . DIRECTORY_SEPARATOR . 'Facades' . DIRECTORY_SEPARATOR . 'Service.php');
		$this->assertFileExists($outputPath . DIRECTORY_SEPARATOR . 'Facades' . DIRECTORY_SEPARATOR . 'SecondService.php');
		$this->assertFileExists($outputPath . DIRECTORY_SEPARATOR . 'Facades' . DIRECTORY_SEPARATOR . 'ServiceFacade.php');
	}

	public function testFacades_()
	{
		$facader = $this->getFacader();

		$outputPath = $facader->getOutputPath();

		$services = [
			'Service'       => [
				Service::class,
			],
			'SecondService' => [
				SecondaryServiceInterface::class => SecondaryService::class,
			],
			'ThirdService'  => [
				Service::class,
				SecondaryServiceInterface::class => SecondaryService::class,
			],
		];

		$facader->services($services);

		$this->assertFileExists($outputPath . DIRECTORY_SEPARATOR . 'Service.php');
		$this->assertFileExists($outputPath . DIRECTORY_SEPARATOR . 'SecondService.php');
		$this->assertFileExists($outputPath . DIRECTORY_SEPARATOR . 'ThirdService.php');
	}


	/**
	 * @return void
	 */
	protected function tearDown() : void
	{
		$facader = $this->getFacader();

		$outputPath = $facader->getOutputPath();

		// drop old
		$it = new \RecursiveDirectoryIterator($outputPath, \RecursiveDirectoryIterator::SKIP_DOTS);
		$iit = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
		foreach ( $iit as $file ) {
			$todo = ( $file->isDir()
				? 'rmdir'
				: 'unlink' );
			$todo($file->getRealPath());
		}

		parent::tearDown();
	}

	/**
	 * @return void
	 */
	protected static function boot() : void
	{
		$di = new Di();
		$di->registerProvider(FacaderProvider::class);
		$di->boot();

		static::$di = $di;

		parent::boot();
	}


	/**
	 * @var Di $di
	 */
	protected static $di;
}