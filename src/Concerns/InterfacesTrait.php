<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Concerns;

use ReflectionClass;

trait InterfacesTrait
{
	/**
	 * 
	 */
	public function interfaces(Object $object)
	{
        $class = new ReflectionClass($object);

        $arr = $class->getInterfaces();

        if (!count($arr)) return [];

        $interfaces = [];

        foreach ($arr as $interface) {
            $interfaces[] = $interface->getName();
        }

        sort($interfaces);

        return $interfaces;		
	}
}