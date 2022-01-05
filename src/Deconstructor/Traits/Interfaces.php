<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Traits;

use ReflectionClass;

trait Interfaces
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