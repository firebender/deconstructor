<?php

/**
 * If the function d() doens't exist, use that
 *
 * If it does, try using dx()
 *
 * If dx() exists, use a lambda function
 */
if (function_exists('d')) {
	if (function_exists('dx')) {
		function get_lambda_deconstructor(mixed $arg = null, bool $return = false)
		{
			return function (mixed $arg = null, bool $return = false) {
				$deconstructor = new \FireBender\Deconstructor();
				return $deconstructor->deconstruct($arg);
			};
		}

	} else {
		function dx(mixed $arg = null, bool $return = false) {
			$deconstructor = new \FireBender\Deconstructor();
			return $deconstructor->deconstruct($arg, $return);
		}
	}
} else {
	function d(mixed $arg = null, bool $return = false) {
		$deconstructor = new \FireBender\Deconstructor();
		return $deconstructor->deconstruct($arg, $return);
	}
}


