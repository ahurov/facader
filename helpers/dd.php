<?php

// vd
// выводит аргументы для просмотра при отладке
if (! function_exists('vd')) {
	function vd(string $tracepath, ...$arguments) : array
	{
		if ($tracepath) array_unshift($arguments, $tracepath);

		switch ( true ):
			case function_exists('dump'):
				dump(...$arguments);
				break;

			case 'cli' === PHP_SAPI && function_exists('pause'):
				pause(...$arguments);
				break;

			default:
				var_dump(...$arguments);
				break;

		endswitch;

		return $arguments;
	}
}

// dp
// выводит аргументы для просмотра при отладке в том числе путь к файлу, откуда отладка была вызвана
if (! function_exists('dp')) {
	function dp(...$arguments) : array
	{
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
		$file = $trace[ 0 ][ 'file' ] ?? '';
		$line = $trace[ 0 ][ 'line' ] ?? 0;

		return vd($file . ':' . $line, ...$arguments);
	}
}

// dt
// выводит аргументы для просмотра при отладке в том числе путь к файлу, откуда отладка была вызвана и завершает программу
if (! function_exists('dt')) {
	function dt(...$arguments) : void
	{
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
		$file = $trace[ 0 ][ 'file' ] ?? '';
		$line = $trace[ 0 ][ 'line' ] ?? 0;

		vd($file . ':' . $line, ...$arguments);

		die(1);
	}
}

// ddt
// выводит аргументы для просмотра при отладке в том числе путь к файлу, откуда отладка была вызвана и завершает программу в случае если функция вызвана $times раз
if (! function_exists('ddt')) {
	function ddt(int $times, ...$arguments) : array
	{
		static $t;

		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
		$file = $trace[ 0 ][ 'file' ] ?? '';
		$line = $trace[ 0 ][ 'line' ] ?? 0;
		$key = $file . ':' . $line;

		$t[ $key ] = $t[ $key ] ?? $times;

		vd($key, ...$arguments);

		if (! --$t[ $key ]) die(1);

		return $arguments;
	}
}

// dde
// выводит аргументы $iteration операции для просмотра при отладке в том числе путь к файлу, откуда отладка была вызвана и завершает программу в случае если функция вызвана $iteration раз
if (! function_exists('dde')) {
	function dde(int $iteration, ...$arguments) : array
	{
		static $t;

		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
		$file = $trace[ 0 ][ 'file' ] ?? '';
		$line = $trace[ 0 ][ 'line' ] ?? 0;
		$key = $file . ':' . $line;

		$t[ $key ] = $t[ $key ] ?? $iteration;


		if (! --$t[ $key ]) {
			vd($key, ...$arguments);

			die(1);
		}

		return $arguments;
	}
}

// stop
// выводит аргументы в консоли и останавливает программу
if (! function_exists('stop')) {
	function stop(...$arguments) : void
	{
		if (PHP_SAPI !== 'cli') {
			throw new \BadFunctionCallException('Should be called in CLI mode');
		}

		pause(...$arguments);

		exit(1);
	}
}

// pause
// выводит аргументы в консоли и ставит программу на паузу
if (! function_exists('pause')) {
	function pause(...$arguments) : array
	{
		if (PHP_SAPI !== 'cli') {
			throw new \BadFunctionCallException('Should be called in CLI mode');
		}

		if ($arguments) var_dump(...$arguments);

		echo '> Press ENTER to continue...' . PHP_EOL;
		$h = fopen('php://stdin', 'r');
		fgets($h);
		fclose($h);

		return $arguments;
	}
}