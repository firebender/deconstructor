<?php

use FireBender\Deconstructor\Deconstructor;

/**
 * If the function d() doens't exist, use that.
 *
 * If it does, try using dx()
 *
 * If dx() exists, use a lambda function
 */
if (function_exists('d')) {
    if (function_exists('dx')) {

        function get_lambda_deconstructor(mixed $arg = null, bool $return = false): Closure
        {
            return function (mixed $arg = null, bool $return = false) {
                $deconstructor = new Deconstructor();

                return $deconstructor->deconstruct($arg);
            };
        }
    } else {
        /**
         * @return array<string, mixed>|Deconstructor
         */
        function dx(mixed $arg = null, bool $return = false): array|Deconstructor
        {
            $deconstructor = new Deconstructor();

            return $deconstructor->deconstruct($arg, $return);
        }
    }
} else {
    /**
     * @return array<string, mixed>|Deconstructor
     */
    function d(mixed $arg = null, bool $return = false): array|Deconstructor
    {
        $deconstructor = new Deconstructor();

        return $deconstructor->deconstruct($arg, $return);
    }
}
