<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Traits;

use Exception;

trait Laravel
{
    /**
     * 
     */
    public function providers($return = false)
    {
        if ($this->inLaravel() === false) {
            $format = 'Cannot call %s if not inside Laravel application';
            $message = sprintf($format, __METHOD__);
            throw new Exception($message);
        }

        if (method_exists(app(), 'getLoadedProviders') === false) {
            $format = "Cannot run %. Method getLoadedProviders() in app() doesn't exist";
            $message = sprintf($format, __METHOD__);
            throw new Exception($message);
        }

        $providers = array_keys(app()->getLoadedProviders());
        sort($providers);

        if ($return === true) return $providers;

        dd($providers);
    }

    /**
     * 
     */
    public function bindings($return = false)
    {
        if ($this->inLaravel() === false) {
            $format = 'Cannot call %s if not inside Laravel application';
            $message = sprintf($format, __METHOD__);
            throw new Exception($message);
        }

        if (method_exists(app(), 'getBindings') === false) {
            $format = "Cannot run %. Method getBindings() in app() doesn't exist";
            $message = sprintf($format, __METHOD__);
            throw new Exception($message);
        }

        $bindings = array_keys(app()->getBindings());
        sort($bindings);

        if ($return === true) return $bindings;

        dd($bindings);
    }

	/**
	 * 
	 */
	protected function inLaravel()
	{
        if (function_exists('app') === false) return false;

        if (method_exists(app(), 'bind') === false) return false;

        return true;
	}


}