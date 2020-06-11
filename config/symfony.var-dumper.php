<?php

use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

( function () {
	$dumper = new class {
		/**
		 * @var array
		 */
		protected $casters = [];
		/**
		 * @var array
		 */
		protected $registry = [];


		/**
		 * Constructor
		 */
		public function __construct()
		{
			$this->casters[ 'isNull' ] = function ($var) {
				return is_null($var);
			};

			$this->registry = [
				// \Gzhegow\Di\Di::class => $this->casters[ 'isNull' ],
			];
		}


		/**
		 * @param $var
		 *
		 * @return void
		 */
		public function dump($var)
		{
			if (PHP_SAPI === 'cli') {
				$this->dumpCli($var);
			} else {
				$this->dumpHtml($var);
			}
		}


		/**
		 * @param $var
		 *
		 * @return void
		 */
		protected function dumpCli($var)
		{
			if (PHP_SAPI !== 'cli') return;

			$cloner = new VarCloner();

			$cloner->addCasters($this->casters);

			$dumper = new CliDumper();
			$dumper->dump($cloner->cloneVar($var));
		}

		/**
		 * @param $var
		 *
		 * @return void
		 */
		protected function dumpHtml($var)
		{
			if (PHP_SAPI === 'cli') return;

			$cloner = new VarCloner();

			$cloner->addCasters($this->casters);

			$dumper = new HtmlDumper();
			$dumper->dump($cloner->cloneVar($var));
		}
	};


	// decorate
	VarDumper::setHandler([ $dumper, 'dump' ]);
} )();