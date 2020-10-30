<?php

use Gzhegow\Facader\Tests\Services\Service;
use Gzhegow\Facader\Tests\Services\SecondaryService;
use Gzhegow\Facader\Tests\Services\SecondaryServiceInterface;

return [
	'services' => [
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
	],
	'facades'  => [
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
	],
];