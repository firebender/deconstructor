<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Concerns;

use Exception;
use Illuminate\Config\Repository;

trait LaravelTrait
{
    /**
     * @return array<int, string>
     */
    public function providers(bool $return = false): array
    {
        $this->inLaravel(__METHOD__);

        try {
            $providers = array_keys(app()->getLoadedProviders());
        } catch (Exception $e) {
            $format = 'Cannot run app()->getLoadedProviders() in %s';
            $message = sprintf($format, __METHOD__);

            throw new Exception($message);
        }

        sort($providers);

        if ($return === true) {
            return $providers;
        }

        dd($providers);
    }

    /**
     * @return array<int, string>
     */
    public function bindings(bool $return = false): array
    {
        $this->inLaravel(__METHOD__);

        try {
            $bindings = array_keys(app()->getBindings());
        } catch (Exception $e) {
            $format = 'Cannot run app()->getBindings() in %s';
            $message = sprintf($format, __METHOD__);

            throw new Exception($message);
        }

        sort($bindings);

        if ($return === true) {
            return $bindings;
        }

        dd($bindings);
    }

    /**
     * @return array<int, string>
     */
    public function config(bool $return = false): array
    {
        $this->inLaravel(__METHOD__);

        $config = config();
        assert($config instanceof Repository);

        $all = $config->all();
        $keys = array_keys($all);
        sort($keys);

        if ($return === true) {
            return $keys;
        }

        dd($keys);
    }

    protected function inLaravel(string $method): bool
    {
        if (function_exists('app') === false) {
            $format = 'Cannot call %s if not inside Laravel application';
            $message = sprintf($format, $method);

            throw new Exception($message);
        }

        return true;
    }
}
