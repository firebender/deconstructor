<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Concerns;

use ReflectionClass;

trait ConstantsTrait
{
	/**
	 * 
	 */
	public function constants(Object $object)
	{
        $class = new ReflectionClass($object);

        $constants = $class->getConstants();
        ksort($constants);

        return $constants;
	}

    /**
     * 
     */
    protected function formattedConstants(Object $object)
    {
        $constants = $this->constants($object);

        $display = [];
        foreach ($constants as $key => $entry) {
            if (is_array($entry)) {
                $s = "[" . PHP_EOL;
                foreach ($entry as $k => $v) {
                    $s .= "$k => $v" . PHP_EOL;
                }
                $s .= "]" . PHP_EOL;
                $entry = $s;
            }            
            $display[] = "<fg=blue;options=bold>$key</> => $entry";
        }

        return $display;
    }

}