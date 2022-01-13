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

        if (get_class(app()) !== 'Illuminate\Foundation\Application') return false;

        return true;
	}


}